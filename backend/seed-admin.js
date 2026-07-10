import path from "node:path";
import { fileURLToPath } from "node:url";
import { DatabaseSync } from "node:sqlite";
import { auth } from "./auth.js";

const __dirname = path.dirname(fileURLToPath(import.meta.url));

const ADMIN_EMAIL = "zion.admin@zionindustries.com";
const ADMIN_PASSWORD = "ZionAdmin!";
const ADMIN_NAME = "Zion Admin";

async function main() {
  const dbPath = path.join(__dirname, "database", "database.sqlite");

  // Delete-then-recreate so the account always matches the constants above
  // (e.g. re-running after fixing a typo'd password actually takes effect).
  const cleanupDb = new DatabaseSync(dbPath);
  const existing = cleanupDb.prepare(`SELECT id FROM user WHERE email = ?`).get(ADMIN_EMAIL);
  if (existing) {
    cleanupDb.prepare(`DELETE FROM session WHERE userId = ?`).run(existing.id);
    cleanupDb.prepare(`DELETE FROM account WHERE userId = ?`).run(existing.id);
    cleanupDb.prepare(`DELETE FROM user WHERE id = ?`).run(existing.id);
    console.log(`Removed existing ${ADMIN_EMAIL} to recreate with current credentials.`);
  }
  cleanupDb.close();

  await auth.api.signUpEmail({
    body: { name: ADMIN_NAME, email: ADMIN_EMAIL, password: ADMIN_PASSWORD },
  });
  console.log(`Created user ${ADMIN_EMAIL}`);

  const db = new DatabaseSync(dbPath);
  db.prepare(`UPDATE user SET role = 'admin' WHERE email = ?`).run(ADMIN_EMAIL);
  db.close();

  console.log(`${ADMIN_EMAIL} is now an admin.`);
}

main();
