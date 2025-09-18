document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('country');
    const stateSelect = document.getElementById('state');

    // Function to fetch countries
    async function fetchCountries() {
        try {
            const response = await fetch('https://restcountries.com/v3.1/all?fields=name');
            const countries = await response.json();
            countries.sort((a, b) => a.name.common.localeCompare(b.name.common)); // Sort alphabetically

            countrySelect.innerHTML = '<option value="">Select Country</option>';
            countries.forEach(country => {
                const option = document.createElement('option');
                option.value = country.name.common;
                option.textContent = country.name.common;
                countrySelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error fetching countries:', error);
            // Fallback for common countries if API fails
            const fallbackCountries = ["Nigeria", "United States", "United Kingdom", "Canada", "Australia", "Germany", "France", "India", "China", "Brazil"];
            countrySelect.innerHTML = '<option value="">Select Country</option>';
            fallbackCountries.forEach(country => {
                const option = document.createElement('option');
                option.value = country;
                option.textContent = country;
                countrySelect.appendChild(option);
            });
        }
    }

    // Function to fetch states based on country (using a simplified approach or a more specific API if available)
    async function fetchStates(country) {
        stateSelect.innerHTML = '<option value="">Select State</option>';
        stateSelect.disabled = true; // Disable until states are loaded or if no states

        if (!country) {
            return;
        }

        // This is a placeholder. In a real application, you would use a robust API
        // that provides states/regions for a given country.
        // For demonstration, we'll use a very basic hardcoded example for a few countries.
        const statesData = {
            "Nigeria": ["Abia", "Adamawa", "Akwa Ibom", "Anambra", "Bauchi", "Bayelsa", "Benue", "Borno", "Cross River", "Delta", "Ebonyi", "Edo", "Ekiti", "Enugu", "Gombe", "Imo", "Jigawa", "Kaduna", "Kano", "Katsina", "Kebbi", "Kogi", "Kwara", "Lagos", "Nasarawa", "Niger", "Ogun", "Ondo", "Osun", "Oyo", "Plateau", "Rivers", "Sokoto", "Taraba", "Yobe", "Zamfara", "Federal Capital Territory"],
            "United States": ["Alabama", "Alaska", "Arizona", "Arkansas", "California", "Colorado", "Connecticut", "Delaware", "Florida", "Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa", "Kansas", "Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts", "Michigan", "Minnesota", "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire", "New Jersey", "New Mexico", "New York", "North Carolina", "North Dakota", "Ohio", "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island", "South Carolina", "South Dakota", "Tennessee", "Texas", "Utah", "Vermont", "Virginia", "Washington", "West Virginia", "Wisconsin", "Wyoming"],
            "Canada": ["Alberta", "British Columbia", "Manitoba", "New Brunswick", "Newfoundland and Labrador", "Nova Scotia", "Ontario", "Prince Edward Island", "Quebec", "Saskatchewan"],
            "United Kingdom": ["England", "Scotland", "Wales", "Northern Ireland"]
        };

        const states = statesData[country];
        if (states && states.length > 0) {
            states.sort();
            states.forEach(state => {
                const option = document.createElement('option');
                option.value = state;
                option.textContent = state;
                stateSelect.appendChild(option);
            });
            stateSelect.disabled = false;
        } else {
            // If no specific states, allow manual input or keep disabled
            stateSelect.disabled = false; // Enable to allow user to skip if no states are provided
            console.warn(`No specific states found for ${country}.`);
        }
    }

    // Event listener for country change
    countrySelect.addEventListener('change', function() {
        fetchStates(this.value);
    });

    // Initial load
    fetchCountries();
});
