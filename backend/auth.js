import path from "node:path";
import { fileURLToPath } from "node:url";
import { DatabaseSync } from "node:sqlite";
import "dotenv/config";
import { betterAuth } from "better-auth";

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export const auth = betterAuth({
  database: new DatabaseSync(path.join(__dirname, "database", "database.sqlite")),
  basePath: "/api/auth",
  emailAndPassword: {
    enabled: true,
  },
  user: {
    additionalFields: {
      role: {
        type: "string",
        defaultValue: "customer",
        input: false,
      },
    },
  },
  trustedOrigins: ["http://localhost:5173"],
});
