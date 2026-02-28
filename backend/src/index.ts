import express from "express";
import dotenv from "dotenv";
import searchGoogleRouter from "./routes/search.google";
import searchSerperRouter from "./routes/search.serper";

dotenv.config();

const app = express();
const port = process.env.PORT || 3000;

app.use(express.json());
// app.use("/api", searchGoogleRouter);
app.use("/api", searchSerperRouter);

app.listen(port, () => {
  console.log(
    `Server running on port ${port}. Go visit 'http://localhost:8000'`,
  );
});
