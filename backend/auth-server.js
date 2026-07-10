import express from "express";
import { toNodeHandler } from "better-auth/node";
import { auth } from "./auth.js";

const app = express();

app.all("/api/auth/*", toNodeHandler(auth));

const port = process.env.AUTH_SERVER_PORT || 3001;
app.listen(port, () => {
  console.log(`auth server listening on :${port}`);
});
