/** services/api.js **/
import axios from 'axios';
import {useUserStore} from "../stores/userStore.js";

// Oppretter en ny bruker
export async function createUser(user, guardian) {
    return await axios.post('/api/users/', { user, guardian });
}

// Logger inn en bruker
export async function loginUser(userId) {
    const userStore = useUserStore();
    try {
        // 1. Logg inn og få brukerdata og token
        const response = await axios.post('/api/login', { user_id: userId });
        const { user, token } = response.data;

        // 2. Oppdater brukerdata og token i Pinia
        userStore.setUser({ user, token });

        // 3. Sett Authorization-headeren for fremtidige forespørsler
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

        try {
            // 4. Forsøk å hente roller og tillatelser fra `api/user`
            const userDetailsResponse = await axios.get('/api/user');
            const { roles, permissions } = userDetailsResponse.data;

            // 5. Oppdater hele brukerdata (inkludert roller og tillatelser) i storen
            userStore.setUser({
                user: userStore.user, // Behold eksisterende brukerdata
                roles: roles,
                permissions: permissions,
                token: userStore.token // Behold eksisterende token
            });
        } catch (error) {
            // Hvis henting mislykkes
            if (error.response && error.response.status === 401) {
                console.warn('Brukeren er ikke autentisert. Roller/tillatelser kunne ikke hentes.');
            } else {
                console.error('En annen feil oppsto ved henting av roller/tillatelser:', error);
            }
        }
    } catch (e) {
        console.error('Error logging in user:', e.message || e.toString());
        throw e; // Send feilen videre slik at den kan håndteres globalt
    }
}




// Henter brukerliste fra serveren
export async function fetchUsers() {
    return await axios.get('/api/users');
}
