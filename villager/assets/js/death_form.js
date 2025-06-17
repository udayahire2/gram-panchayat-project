// Modal to view uploaded document (Aadhaar, etc.)
function viewDocument(documentPath, documentTitle) {
    const docContainer = document.getElementById('docContainer');
    const docModalTitle = document.getElementById('docModalTitle');

    docModalTitle.textContent = documentTitle;
    const fileExt = documentPath.split('.').pop().toLowerCase();

    if (fileExt === 'pdf') {
        docContainer.innerHTML = `<embed src="${documentPath}" type="application/pdf" width="100%" height="600px" />`;
    } else if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
        docContainer.innerHTML = `<img src="${documentPath}" alt="${documentTitle}" />`;
    } else {
        docContainer.innerHTML = `<p>Unable to preview this file type. <a href="${documentPath}" target="_blank">Download instead</a></p>`;
    }

    document.getElementById('documentModal').style.display = 'flex';
}

// Modal to view death certificate application details
function viewDetails(recordId) {
    fetch(`get_death_record.php?id=${recordId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Safe data extraction
            const safeData = {
                name_of_deceased: data.name_of_deceased || 'Not provided',
                gender: data.gender || 'Not provided',
                birth_date: data.birth_date || null,
                age: data.age || 'Not provided',
                aadhaar_number: data.aadhaar_number || 'Not provided',
                date_of_death: data.date_of_death || null,
                place_of_death: data.place_of_death || 'Not provided',
                address: data.address || 'Not provided',
                registration_date: data.registration_date || null,
                status: data.status || 'PENDING',
                created_at: data.created_at || null,
                updated_at: data.updated_at || null,
                remarks: data.remarks || 'No remarks',
                villager_email: data.villager_email || 'Not provided'
            };

            let detailsHtml = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h3>Deceased Details</h3>
                        <p><strong>Name:</strong> ${safeData.name_of_deceased}</p>
                        <p><strong>Gender:</strong> ${safeData.gender}</p>
                        <p><strong>Birth Date:</strong> ${safeData.birth_date ? formatDate(safeData.birth_date) : 'Not provided'}</p>
                        <p><strong>Age at Death:</strong> ${safeData.age}</p>
                        <p><strong>Aadhaar Number:</strong> ${safeData.aadhaar_number}</p>
                    </div>
                    <div>
                        <h3>Death & Registration</h3>
                        <p><strong>Date of Death:</strong> ${safeData.date_of_death ? formatDate(safeData.date_of_death) : 'Not provided'}</p>
                        <p><strong>Place of Death:</strong> ${safeData.place_of_death}</p>
                        <p><strong>Registration Date:</strong> ${safeData.registration_date ? formatDate(safeData.registration_date) : 'Not provided'}</p>
                        <p><strong>Status:</strong> <span class="status-badge status-${safeData.status.toLowerCase()}">${safeData.status}</span></p>
                        <p><strong>Email:</strong> ${safeData.villager_email}</p>
                    </div>
                    <div style="grid-column: span 2;">
                        <h3>Additional Information</h3>
                        <p><strong>Address:</strong> ${safeData.address}</p>
                        <p><strong>Created At:</strong> ${safeData.created_at ? formatDateTime(safeData.created_at) : 'Not available'}</p>
                        ${safeData.updated_at ? `<p><strong>Updated At:</strong> ${formatDateTime(safeData.updated_at)}</p>` : ''}
                        <p><strong>Remarks:</strong> ${safeData.remarks}</p>
                    </div>
                </div>
            `;

            document.getElementById('detailsContainer').innerHTML = detailsHtml;
            document.getElementById('detailsModal').style.display = 'flex';
        })
        .catch(error => {
            console.error('Error fetching record details:', error);
            alert('Failed to load record details. Please try again.');
        });
}

// Utility functions for formatting
function formatDate(dateString) {
    if (!dateString) return 'Not provided';
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'Invalid date';
        return date.toLocaleDateString('en-IN');
    } catch (e) {
        return 'Invalid date format';
    }
}

function formatDateTime(dateTimeString) {
    if (!dateTimeString) return 'Not provided';
    try {
        const date = new Date(dateTimeString);
        if (isNaN(date.getTime())) return 'Invalid date';
        return date.toLocaleDateString('en-IN') + ' ' + date.toLocaleTimeString('en-IN');
    } catch (e) {
        return 'Invalid date format';
    }
}

// Modal close logic
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

window.onclick = function (event) {
    const modals = document.getElementsByClassName('modal');
    for (let i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = 'none';
        }
    }
}