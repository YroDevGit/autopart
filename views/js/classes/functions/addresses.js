// Base API URL
const BASE_URL = 'https://psgc.gitlab.io/api';

// Helper function to format city names
function formatCityName(name) {
    // Convert "City of Himamaylan" to "Himamaylan City"
    if (name.startsWith('City of ')) {
        return name.replace('City of ', '') + ' City';
    }
    // Convert "Municipality of [Name]" to "[Name]"
    if (name.startsWith('Municipality of ')) {
        return name.replace('Municipality of ', '');
    }
    return name;
}

// Get all provinces
export async function getProvince() {
    try {
        const response = await fetch(`${BASE_URL}/provinces`);
        const provinces = await response.json();
        return provinces.sort((a, b) => a.name.localeCompare(b.name));
    } catch (error) {
        console.error('Error fetching provinces:', error);
        return [];
    }
}

// Get municipalities/cities by province code
export async function getMunicipality(provinceId) {
    try {
        const response = await fetch(`${BASE_URL}/provinces/${provinceId}/cities-municipalities`);
        const municipalities = await response.json();
        return municipalities
            .map(item => ({
                ...item,
                name: formatCityName(item.name)
            }))
            .sort((a, b) => a.name.localeCompare(b.name));
    } catch (error) {
        console.error('Error fetching municipalities:', error);
        return [];
    }
}

// Get barangays by city/municipality code
export async function getBaranggay(cityId) {
    try {
        const response = await fetch(`${BASE_URL}/cities-municipalities/${cityId}/barangays`);
        const barangays = await response.json();
        return barangays.sort((a, b) => a.name.localeCompare(b.name));
    } catch (error) {
        console.error('Error fetching barangays:', error);
        return [];
    }
}