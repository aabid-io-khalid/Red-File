import Navbar from './components/Navbar';
import HomePage from './pages/HomePage';
import SignUpPage from './pages/SignUpPage';
import LoginPage from './pages/LoginPage';
import SettingsPage from './pages/SettingsPage';
import ProfilePage from './pages/ProfilePage';
import GroupManagement from './components/GroupManagement';
import ChatContainer from './components/ChatContainer';
import { Routes, Route, Navigate, useLocation } from 'react-router-dom';
import { useAuthStore } from './store/useAuthStore';
import { useThemeStore } from './store/useThemeStore';
import { useEffect } from 'react';
import { Loader } from 'lucide-react';
import { Toaster } from 'react-hot-toast';

const App = () => {
  const { authUser, checkAuth, setAuthFromLaravel, isCheckingAuth } = useAuthStore();
  const { theme } = useThemeStore();
  const location = useLocation();

  useEffect(() => {
    const params = new URLSearchParams(location.search);
    const token = params.get('token');
    console.log('App.jsx - Token from URL:', token);

    if (token) {
      setAuthFromLaravel(token)
        .then((newUser) => {
          console.log('App.jsx - Auth set from Laravel:', newUser);
        })
        .catch((error) => {
          console.error('App.jsx - Failed to set auth from Laravel:', error);
        });
    } else {
      checkAuth();
    }
  }, [checkAuth, setAuthFromLaravel, location.search]);

  if (isCheckingAuth) {
    console.log('App.jsx - isCheckingAuth:', isCheckingAuth, 'authUser:', authUser);
    return (
      <div className="flex items-center justify-center h-screen">
        <Loader className="size-10 animate-spin" />
      </div>
    );
  }

  return (
    <div data-theme={theme}>
      <Navbar />
      <Routes>
        <Route path="/" element={authUser ? <HomePage /> : <Navigate to="/login" />}>
          <Route path="chat" element={<ChatContainer />} />
          <Route path="groups" element={<GroupManagement userId={authUser?.id} />} />
        </Route>
        <Route path="/signup" element={!authUser ? <SignUpPage /> : <Navigate to="/" />} />
        <Route path="/login" element={!authUser ? <LoginPage /> : <Navigate to="/" />} />
        <Route path="/settings" element={<SettingsPage />} />
        <Route path="/profile" element={authUser ? <ProfilePage /> : <Navigate to="/login" />} />
      </Routes>
      <Toaster />
    </div>
  );
};

export default App;