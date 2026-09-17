import http from "node:http";
import { spawn, execSync, ChildProcess } from "node:child_process";
import express from "express";

const app = express();
const PORT = 3000;
let phpProcess: ChildProcess | null = null;
let isInstallingPhp = false;

function ensurePhpInstalled(): boolean {
  try {
    execSync("which php", { stdio: "ignore" });
    return true;
  } catch {
    if (isInstallingPhp) return false;
    isInstallingPhp = true;
    try {
      console.log("PHP not found in environment. Attempting automatic installation...");
      execSync(
        "apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y php-cli php-sqlite3 -o Dpkg::Options::='--force-confdef' -o Dpkg::Options::='--force-confold'",
        { stdio: "inherit" }
      );
      isInstallingPhp = false;
      return true;
    } catch (e) {
      console.error("Failed to install PHP automatically:", e);
      isInstallingPhp = false;
      return false;
    }
  }
}

function startPhpServer() {
  if (phpProcess) return;

  if (!ensurePhpInstalled()) {
    console.error("PHP is currently not available.");
    return;
  }

  try {
    try {
      execSync("pkill -f 'php -S 127.0.0.1:8080' 2>/dev/null || true", { stdio: "ignore" });
    } catch {
      // ignore
    }

    phpProcess = spawn("php", ["-S", "127.0.0.1:8080", "-t", "."], {
      stdio: "inherit"
    });

    phpProcess.on("error", (err) => {
      console.error("PHP process error:", err.message);
      phpProcess = null;
    });

    phpProcess.on("exit", (code) => {
      console.log(`PHP process exited with code ${code}`);
      phpProcess = null;
    });
  } catch (err) {
    console.error("Failed to spawn PHP server:", err);
    phpProcess = null;
  }
}

startPhpServer();

function killPhpProcess() {
  if (phpProcess) {
    try {
      phpProcess.kill("SIGTERM");
    } catch {
      // ignore
    }
    phpProcess = null;
  }
  try {
    execSync("pkill -f 'php -S 127.0.0.1:8080' 2>/dev/null || true", { stdio: "ignore" });
  } catch {
    // ignore
  }
}

process.on("exit", killPhpProcess);
process.on("SIGINT", () => {
  killPhpProcess();
  process.exit();
});
process.on("SIGTERM", () => {
  killPhpProcess();
  process.exit();
});

app.use((req, res) => {
  if (!phpProcess) {
    startPhpServer();
  }

  const forwardRequest = (retryCount = 0) => {
    const clientReq = http.request(
      {
        hostname: "127.0.0.1",
        port: 8080,
        path: req.url,
        method: req.method,
        headers: req.headers
      },
      (clientRes) => {
        const responseHeaders = { ...clientRes.headers };
        if (responseHeaders["set-cookie"]) {
          const rawCookies = Array.isArray(responseHeaders["set-cookie"])
            ? responseHeaders["set-cookie"]
            : [responseHeaders["set-cookie"]];
          responseHeaders["set-cookie"] = rawCookies.map((cookieStr) => {
            let mod = cookieStr;
            if (!/;\s*Secure/i.test(mod)) mod += "; Secure";
            if (!/;\s*SameSite=/i.test(mod)) {
              mod += "; SameSite=None";
            } else {
              mod = mod.replace(/;\s*SameSite=[^;]+/i, "; SameSite=None");
            }
            if (!/;\s*Partitioned/i.test(mod)) mod += "; Partitioned";
            return mod;
          });
        }
        res.writeHead(clientRes.statusCode || 200, responseHeaders);
        clientRes.pipe(res);
      }
    );

    clientReq.on("error", (err) => {
      if (retryCount < 5) {
        setTimeout(() => forwardRequest(retryCount + 1), 500);
      } else {
        res.status(502).send("Starting PHP backend server. Please refresh in a moment.");
      }
    });

    req.pipe(clientReq);
  };

  forwardRequest();
});

app.listen(PORT, "0.0.0.0", () => {
  console.log(`Server running on port ${PORT}`);
});

