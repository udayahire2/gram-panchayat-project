function showStatusModal(recordId, status) {
    document.getElementById('record_id').value = recordId;
    document.getElementById('new_status').value = status;
    const modalTitle = document.getElementById('modalTitle');
    const confirmBtn = document.getElementById('confirmBtn');

    if (status === 'APPROVED') {
        modalTitle.textContent = 'Approve Birth Certificate';
        confirmBtn.textContent = 'Approve';
        confirmBtn.className = 'modal-btn btn-approve';
    } else {
        modalTitle.textContent = 'Reject Birth Certificate';
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
    fetch(`get_birth_record.php?id=${recordId}`)
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

            let detailsHtml = `
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <div>
            <p><strong>Name:</strong> ${data.name}</p>
            <p><strong>Gender:</strong> ${data.gender || 'N/A'}</p>
            <p><strong>Date of Birth:</strong> ${formatDate(data.dob)}</p>
            <p><strong>Place of Birth:</strong> ${data.place_of_birth || 'N/A'}</p>
            <p><strong>Mother Name:</strong> ${data.mother_name}</p>
            <p><strong>Mother Aadhaar:</strong> ${data.mother_aadhaar}</p>
            <p><strong>Father Name:</strong> ${data.father_name}</p>
            <p><strong>Father Aadhaar:</strong> ${data.father_aadhaar}</p>
            </div>
            <div>
            <p><strong>Address:</strong> ${data.address || 'N/A'}</p>
            <p><strong>Registration Date:</strong> ${formatDate(data.registration_date)}</p>
            <p><strong>Status:</strong> <span class="status-badge status-${data.status.toLowerCase()}">${data.status}</span></p>
            <p><strong>Email:</strong> ${data.villager_email}</p>
            <p><strong>Created At:</strong> ${formatDateTime(data.created_at)}</p>
            ${data.updated_at ? `<p><strong>Updated At:</strong> ${formatDateTime(data.updated_at)}</p>` : ''}
            ${data.remarks ? `<p><strong>Remarks:</strong> ${data.remarks}</p>` : ''}
        </div>
    </div>
`;

            document.getElementById('detailsContainer').innerHTML = detailsHtml;
            document.getElementById('detailsModal').style.display = 'flex';
        })
        .catch(error => {
            console.error('Error fetching record details:', error);
            alert('Failed to load record details');
        });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN');
}

function formatDateTime(dateTimeString) {
    const date = new Date(dateTimeString);
    return date.toLocaleDateString('en-IN') + ' ' + date.toLocaleTimeString('en-IN');
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