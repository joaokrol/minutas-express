// api/index.js
import express from "express";
import cors from "cors";

const app = express();
app.use(cors());
app.use(express.json());

app.get("/", (req, res) => res.send("Hello from Express on Vercel! 🚀"));
app.get("/health", (req, res) => res.json({ ok: true }));

// Exporta handler (sem app.listen)
export default function handler(req, res) {
  return app(req, res);
}
