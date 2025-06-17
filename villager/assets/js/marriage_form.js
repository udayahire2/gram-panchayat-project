function showStatusModal(recordId, status) {
    document.getElementById('record_id').value = recordId;
    document.getElementById('new_status').value = status;
    const modalTitle = document.getElementById('modalTitle');
    const confirmBtn = document.getElementById('confirmBtn');

    if (status === 'APPROVED') {
        modalTitle.textContent = 'Approve Marriage Certificate';
        confirmBtn.textContent = 'Approve';
        confirmBtn.className = 'modal-btn btn-approve';
    } else {
        modalTitle.textContent = 'Reject Marriage Certificate';
        confirmBtn.textContent = 'Reject';
        confirmBtn.className = 'modal-btn btn-reject';
    }

    document.getElementById('statusModal').style.display = 'flex';
}

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

function viewDetails(recordId) {
    fetch(`get_marriage_record.php?id=${recordId}`)
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

            // Check if all required fields exist and handle null/undefined values
            const safeData = {
                husband_name: data.husband_name || 'Not provided',
                husband_age: data.husband_age || 'Not provided',
                husband_aadhar_no: data.husband_aadhar_no || 'Not provided',
                husband_dob: data.husband_dob || null,
                husband_cast: data.husband_cast || 'Not provided',
                husband_address: data.husband_address || 'Not provided',
                wife_name: data.wife_name || 'Not provided',
                wife_age: data.wife_age || 'Not provided',
                wife_aadhar_no: data.wife_aadhar_no || 'Not provided',
                wife_dob: data.wife_dob || null,
                wife_cast: data.wife_cast || 'Not provided',
                wife_address: data.wife_address || 'Not provided',
                witness1_name: data.witness1_name || 'Not provided',
                witness2_name: data.witness2_name || 'Not provided',
                witness3_name: data.witness3_name || 'Not provided',
                marriage_date: data.marriage_date || null,
                registration_date: data.registration_date || null,
                marriage_place: data.marriage_place || 'Not provided',
                status: data.status || 'PENDING',
                created_at: data.created_at || null,
                updated_at: data.updated_at || null,
                remarks: data.remarks || 'No remarks',
                email: data.email || 'Not provided'
            };

            let detailsHtml = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Spouse Details -->
                    <div>
                        <h3>Spouse Details</h3>
                        <p><strong>Husband Name:</strong> ${safeData.husband_name}</p>
                        <p><strong>Husband Age:</strong> ${safeData.husband_age}</p>
                        <p><strong>Husband Aadhaar:</strong> ${safeData.husband_aadhar_no}</p>
                        <p><strong>Husband DOB:</strong> ${safeData.husband_dob ? formatDate(safeData.husband_dob) : 'Not provided'}</p>
                        <p><strong>Husband Cast:</strong> ${safeData.husband_cast}</p>
                        <p><strong>Husband Address:</strong> ${safeData.husband_address}</p>
                        <p><strong>Wife Name:</strong> ${safeData.wife_name}</p>
                        <p><strong>Wife Age:</strong> ${safeData.wife_age}</p>
                        <p><strong>Wife Aadhaar:</strong> ${safeData.wife_aadhar_no}</p>
                        <p><strong>Wife DOB:</strong> ${safeData.wife_dob ? formatDate(safeData.wife_dob) : 'Not provided'}</p>
                        <p><strong>Wife Cast:</strong> ${safeData.wife_cast}</p>
                        <p><strong>Wife Address:</strong> ${safeData.wife_address}</p>
                    </div>

                    <!-- Marriage & Registration Details -->
                    <div>
                        <h3>Marriage Details</h3>
                        <p><strong>Witness 1:</strong> ${safeData.witness1_name}</p>
                        <p><strong>Witness 2:</strong> ${safeData.witness2_name}</p>
                        <p><strong>Witness 3:</strong> ${safeData.witness3_name}</p>
                        </div>
                        
                        <!-- Witness Details -->
                        <div>
                        <h3>Registration Details</h3>
                        
                        <p><strong>Marriage Date:</strong> ${safeData.marriage_date ? formatDate(safeData.marriage_date) : 'Not provided'}</p>
                        <p><strong>Registration Date:</strong> ${safeData.registration_date ? formatDate(safeData.registration_date) : 'Not provided'}</p>
                        <p><strong>Place:</strong> ${safeData.marriage_place}</p>
                        <p><strong>Status:</strong> 
                        <span class="status-badge status-${safeData.status.toLowerCase()}">${safeData.status}</span>
                        <p><strong>Email:</strong> ${safeData.email}</p>
                        </p>
                    </div>

                    <!-- Additional Details -->
                    <div style="grid-column: span 2;">
                        <h3>Additional Information</h3>
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