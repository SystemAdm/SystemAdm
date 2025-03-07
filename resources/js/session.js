import axios from "axios";
import { notify } from "notiwind";
import { ref } from "vue";

const user = ref(null);

const handleLogout = async (router) => { // Router sendes som parameter
    try {
        await axios.post('/api/logout');
        clearUserSession();
        router.push('/'); // Bruk router for å navigere til startsiden
    } catch (error) {
        console.error('Logout failed:', error);
        notify({
            group: "error",
            title: "Error",
            text: "Failed to log out",
        });
    }
};

const clearUserSession = () => {
    user.value = null;
    localStorage.removeItem('user');
    localStorage.removeItem('token');
    delete axios.defaults.headers.common['Authorization'];

    notify({
        group: "success",
        title: "Success",
        text: "Logged out successfully",
    });
};

const fetchAuthenticatedUser = async () => {
    try {
        const response = await axios.get('/api/user');
        user.value = response.data;

        if (user.value) {
            localStorage.setItem('user', JSON.stringify(user.value));
            window.Laravel.permissions = response.data.permissions || [];
            window.Laravel.roles = response.data.roles || [];
        }
    } catch (error) {
        if (error.response?.status === 401) {
            clearUserSession();
        } else {
            console.error('Error fetching user:', error);
        }
    }
};

export const useSession = () => {
    return {
        user,
        fetchAuthenticatedUser,
        handleLogout, // Merk: forventer router som argument
        clearUserSession,
    };
};
