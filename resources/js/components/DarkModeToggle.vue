<template>
    <div class="flex justify-center items-center p-4">
        <button
            @click="toggleDarkMode"
            class="bg-gray-300 dark:bg-gray-700 text-black dark:text-white px-4 py-2 rounded-full shadow-md"
        >
            {{ isDarkMode ? "Lysmodus" : "Mørkmodus" }}
        </button>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

// Opprett en variabel for å spore current mode
const isDarkMode = ref(false);

// Funksjon for å toggle dark mode
const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add("dark"); // Legg til 'dark' på <html>
        localStorage.setItem("theme", "dark"); // Husk preferansen
    } else {
        document.documentElement.classList.remove("dark"); // Fjern 'dark' fra <html>
        localStorage.setItem("theme", "light");
    }
};

// Sjekk brukerens lagrede preferanse, eller standard systeminstilling
onMounted(() => {
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark" || (!savedTheme && window.matchMedia("(prefers-color-scheme: dark)").matches)) {
        document.documentElement.classList.add("dark");
        isDarkMode.value = true;
    }
});
</script>

<style scoped>
/* Valgfritt: stil på knappen, juster etter behov */
</style>
