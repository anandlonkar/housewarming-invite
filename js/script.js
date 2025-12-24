// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('rsvpForm');
    const rsvpButtons = document.querySelectorAll('.circle-rsvp-btn');
    const responseInput = document.getElementById('response');
    const messageDiv = document.getElementById('message');
    const submitBtn = document.querySelector('.submit-btn');
    const formContainer = document.getElementById('rsvpFormContainer');

    // Handle RSVP button clicks (Going/Can't Go)
    rsvpButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            rsvpButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Set hidden input value
            responseInput.value = this.dataset.response;

            // Show the form
            formContainer.style.display = 'block';
            
            // Scroll to form smoothly
            setTimeout(() => {
                formContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        });
    });

    // Form validation and submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Clear previous messages
        messageDiv.className = 'message';
        messageDiv.textContent = '';

        // Validate response selection
        if (!responseInput.value) {
            showMessage('Please select Going or Can\'t Go', 'error');
            return;
        }

        // Validate required fields
        const name = document.getElementById('name').value.trim();
        if (!name) {
            showMessage('Please enter your name', 'error');
            return;
        }

        // Get form data
        const formData = new FormData(form);

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';

        try {
            // Send RSVP to server
            const response = await fetch('api/rsvp.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showMessage(result.message || 'Thank you for your RSVP!', 'success');
                form.reset();
                rsvpButtons.forEach(btn => btn.classList.remove('active'));
                responseInput.value = '';
                
                // Scroll to message
                messageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Optionally hide form after successful submission
                setTimeout(() => {
                    formContainer.style.display = 'none';
                }, 3000);
            } else {
                showMessage(result.message || 'Sorry, there was an error submitting your RSVP. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('Sorry, there was an error submitting your RSVP. Please try again.', 'error');
        } finally {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit RSVP';
        }
    });

    // Show message function
    function showMessage(text, type) {
        messageDiv.textContent = text;
        messageDiv.className = 'message ' + type;
        messageDiv.style.display = 'block';
    }

    // Phone number formatting (optional enhancement)
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            // Format as (XXX) XXX-XXXX for US numbers
            if (value.length > 0) {
                if (value.length <= 3) {
                    value = '(' + value;
                } else if (value.length <= 6) {
                    value = '(' + value.slice(0, 3) + ') ' + value.slice(3);
                } else {
                    value = '(' + value.slice(0, 3) + ') ' + value.slice(3, 6) + '-' + value.slice(6, 10);
                }
            }
            
            e.target.value = value;
        });
    }
});
