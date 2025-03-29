import express from "express";
import { protectRoute } from "../middleware/auth.middleware.js";
import { getChatMessages, getUsersForSidebar, sendMessage } from "../controllers/message.controller.js"; // ✅ Fixed function name

const router = express.Router();

router.get("/users", protectRoute, getUsersForSidebar);
router.get("/:id", protectRoute, getChatMessages); 
router.post("/send/:id", protectRoute, sendMessage);

export default router;
