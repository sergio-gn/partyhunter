document.addEventListener('DOMContentLoaded', function() {
    // Add the event listener once the DOM is fully loaded
    const form = document.getElementById('upload-avatar-form');
    if (form) {  // Ensure the form element exists
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);
            var errorMessage = document.getElementById('upload-error');

            // Clear any previous error message
            errorMessage.textContent = '';

            // Show a loading message or indicator
            var loadingMessage = document.getElementById('upload-loading');
            loadingMessage.style.display = 'block';

            // Perform the AJAX request
            fetch(ajax_obj.ajax_url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-WP-Nonce': ajax_obj.nonce  // Pass the nonce for security
                }
            })
            .then(response => {
                return response.text();  // Get raw response text first
            })
            .then(data => {
                try {
                    // Attempt to parse the JSON response
                    const jsonResponse = JSON.parse(data);

                    if (jsonResponse.success) {
                        // If successful, show the image URL and a success message
                        console.log('Upload successful:', jsonResponse);
                        document.getElementById('avatar-image').src = jsonResponse.data.image_url;
                        errorMessage.textContent = '';  // Clear any existing error message
                        loadingMessage.style.display = 'none';  // Hide loading message
                    } else {
                        // If not successful, display the error message
                        console.error('Error:', jsonResponse.message);
                        errorMessage.textContent = jsonResponse.message;
                        loadingMessage.style.display = 'none';  // Hide loading message
                    }
                } catch (error) {
                    // If the response cannot be parsed as JSON
                    console.error('JSON parsing error:', error, data);
                    errorMessage.textContent = 'Failed to parse response. Please try again.';
                    loadingMessage.style.display = 'none';  // Hide loading message
                }
            })
            .catch(error => {
                // If there's a network or fetch error
                console.error('Error:', error);
                errorMessage.textContent = 'An error occurred while uploading the image.';
                loadingMessage.style.display = 'none';  // Hide loading message
            });
        });
    }
});