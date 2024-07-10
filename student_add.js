function confirmCreate() {
    const studentId = document.getElementById('student-id').value;
    const firstName = document.getElementById('first-name').value;
    const middleName = document.getElementById('middle-name').value || '';
    const lastName = document.getElementById('last-name').value;
    const year = document.getElementById('years').value;
    const section = document.getElementById('sections').value;
    const program = document.getElementById('programs').value;
    const birthDate = document.getElementById('date-input').value;
    const email = document.getElementById('email').value;

    if (!studentId || !firstName || !lastName || !year || !section || !program || !birthDate || !email) {
        alert('Please fill out all required fields.');
        return;
    }

    if (!confirm(`Are you sure you want to add the following student?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('student_id', studentId);
    formData.append('first_name', firstName);
    formData.append('middle_name', middleName);
    formData.append('last_name', lastName);
    formData.append('student_year', year); // Ensure these match the 'name' attributes in HTML
    formData.append('student_section', section);
    formData.append('student_program', program);
    formData.append('date', birthDate);
    formData.append('email', email);

    fetch('student_add.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.success);
            window.location.href = 'student_record.html';
        } else {
            if (data.error && data.error.includes('Duplicate entry')) {
                alert('Student ID already exists. Please choose a different ID.');
            } else {
                alert(data.error || 'Unknown error occurred.');
            }
        }
    })
    .catch(error => console.error('Error adding student:', error));    
}
