document.addEventListener('DOMContentLoaded', function() {
    const userForm = document.getElementById('userForm');
    const tableContainer = document.getElementById('tableContainer');

    // Load table on page load
    loadTable();

    // Handle form submission
    userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(userForm);
        
        // Show loading state
        const submitBtn = userForm.querySelector('.submit-btn');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Adding...';
        submitBtn.disabled = true;

        fetch('add_user.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('User added successfully!', 'success');
                userForm.reset();
                loadTable(); // Reload table to show new record
            } else {
                showMessage(data.message || 'Error adding user', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred while adding the user', 'error');
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });

    // Function to load table data
    function loadTable() {
        tableContainer.innerHTML = '<div class="loading">Loading users...</div>';

        fetch('get_users.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayTable(data.users);
            } else {
                tableContainer.innerHTML = `<div class="error">${data.message || 'Error loading users'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tableContainer.innerHTML = '<div class="error">An error occurred while loading users</div>';
        });
    }

    // Function to display table
    function displayTable(users) {
        if (users.length === 0) {
            tableContainer.innerHTML = '<div class="loading">No users found</div>';
            return;
        }

        let tableHTML = `
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
        `;

        users.forEach(user => {
            const statusClass = user.status == 1 ? 'status-active' : 'status-inactive';
            const statusText = user.status == 1 ? 'Active' : 'Inactive';
            const toggleClass = user.status == 1 ? 'active' : '';
            const toggleText = user.status == 1 ? 'Deactivate' : 'Activate';

            tableHTML += `
                <tr>
                    <td>${user.id}</td>
                    <td>${escapeHtml(user.name)}</td>
                    <td>${user.age}</td>
                    <td class="${statusClass}">${statusText}</td>
                    <td>
                        <button class="toggle-btn ${toggleClass}" onclick="toggleStatus(${user.id}, ${user.status})">
                            ${toggleText}
                        </button>
                    </td>
                </tr>
            `;
        });

        tableHTML += `
                </tbody>
            </table>
        `;

        tableContainer.innerHTML = tableHTML;
    }

    // Function to toggle user status
    window.toggleStatus = function(userId, currentStatus) {
        const newStatus = currentStatus == 1 ? 0 : 1;
        
        fetch('toggle_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: userId,
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadTable(); // Reload table to reflect changes
                showMessage('Status updated successfully!', 'success');
            } else {
                showMessage(data.message || 'Error updating status', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred while updating status', 'error');
        });
    };

    // Function to show messages
    function showMessage(message, type) {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.success, .error');
        existingMessages.forEach(msg => msg.remove());

        // Create new message
        const messageDiv = document.createElement('div');
        messageDiv.className = type;
        messageDiv.textContent = message;

        // Insert message after the form
        const formSection = document.querySelector('.form-section');
        formSection.appendChild(messageDiv);

        // Auto-remove message after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }

    // Function to escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}); 