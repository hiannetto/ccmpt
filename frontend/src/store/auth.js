import { reactive } from 'vue';

const state = reactive({
  token: localStorage.getItem('token') || null,
  user: JSON.parse(localStorage.getItem('user')) || null,
});

export const useAuth = () => {
  const setAuth = (token, user) => {
    state.token = token;
    state.user = user;
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
  };

  const logout = () => {
    state.token = null;
    state.user = null;
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  };

  const isAuthenticated = () => {
    return !!state.token;
  };

  const hasRole = (role) => {
    return state.user && state.user.role === role;
  };

  return {
    state,
    setAuth,
    logout,
    isAuthenticated,
    hasRole
  };
};
