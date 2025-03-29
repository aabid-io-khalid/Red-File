import express from 'express';
import { 
    createGroup, 
    getAllGroups, 
    joinGroup, 
    leaveGroup, 
    deleteGroup,
    sendMessageToGroup, 
    getGroupMessages, 
    getGroupsForUser,
    getGroupMembers
} from '../controllers/group.controller.js';
import { protectRoute } from '../middleware/auth.middleware.js';

const router = express.Router();

router.use(protectRoute);

router.post('/', createGroup);
router.get('/', getGroupsForUser);
router.get('/available', getAllGroups);
router.delete('/:groupId', deleteGroup);  

// Group membership
router.post('/:groupId/join', joinGroup);
router.post('/:groupId/leave', leaveGroup);
router.get('/:groupId/members', getGroupMembers);  

// Group messages
router.post('/:groupId/messages', sendMessageToGroup);
router.get('/:groupId/messages', getGroupMessages);

export default router;