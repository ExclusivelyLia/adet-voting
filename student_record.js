document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const studentsTableBody = document.getElementById('candidatesTableBody');
    const filterButton = document.querySelector('.filter');

    let filterMode = 0; // Initialize filterMode here

    // Check for the necessary elements before proceeding
    if (searchInput && studentsTableBody) {
        searchInput.addEventListener('input', function() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            fetchStudents()
                .then(students => {
                    const filteredStudents = students.filter(student =>
                        (student.student_id && student.student_id.toLowerCase().includes(searchTerm)) ||
                        (student.student_fname && student.student_fname.toLowerCase().includes(searchTerm)) ||
                        (student.student_mname && student.student_mname.toLowerCase().includes(searchTerm)) ||
                        (student.student_lname && student.student_lname.toLowerCase().includes(searchTerm)) ||
                        (student.student_year && student.student_year.toLowerCase().includes(searchTerm)) ||
                        (student.student_section && student.student_section.toLowerCase().includes(searchTerm)) ||
                        (student.student_program && student.student_program.toLowerCase().includes(searchTerm)) ||
                        (student.student_email && student.student_email.toLowerCase().includes(searchTerm))
                    );
                    populateStudentsTable(filteredStudents);
                })
                .catch(error => {
                    console.error('Error fetching or filtering students:', error);
                });
        });

        fetchStudents()
            .then(students => {
                populateStudentsTable(students);
            })
            .catch(error => {
                console.error('Error fetching initial students:', error);
            });
    } else {
        console.error('Required elements (searchInput or studentsTableBody) not found in the DOM.');
    }

    // Filter button event listener
    if (filterButton) {
        filterButton.addEventListener('click', handleFilterButtonClick);
    } else {
        console.error('Filter button not found in the DOM.');
    }

    // Event delegation for delete buttons
    studentsTableBody.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-button')) {
            const studentId = event.target.dataset.studentId;
            deleteStudent(studentId);
        }
    });

    // Function to fetch students
    function fetchStudents() {
        return fetch('student_record.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .catch(error => {
                console.error('Error fetching students:', error);
                throw error;
            });
    }

    // Function to populate students table
    function populateStudentsTable(students) {
        const tableBody = document.getElementById("candidatesTableBody");
        tableBody.innerHTML = "";

        students.forEach(student => {
            let row = "<tr>";
            row += `<td>${student.student_id || ''}</td>`;
            row += `<td>${student.student_fname || ''}</td>`;
            row += `<td>${student.student_mname || ''}</td>`;
            row += `<td>${student.student_lname || ''}</td>`;
            row += `<td>${student.student_year || ''}</td>`;
            row += `<td>${student.student_section || ''}</td>`;
            row += `<td>${student.student_program || ''}</td>`;
            row += `<td>${student.birth_date || ''}</td>`;
            row += `<td>${student.student_email || ''}</td>`;
            row += `<td>
                        <button class="delete-button" data-student-id="${student.student_id}">Delete</button>
                    </td>`;
            row += "</tr>";
            tableBody.innerHTML += row;
        });
    }

    // Function to delete student
    function deleteStudent(studentId) {
        if (confirm("Are you sure you want to delete this student?")) {
            fetch(`student_delete.php`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${studentId}`,
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log(`Student with ID ${studentId} deleted successfully.`);
                    // Refresh the student table
                    fetchStudents()
                        .then(students => {
                            populateStudentsTable(students);
                        })
                        .catch(error => {
                            console.error('Error fetching initial students:', error);
                        });
                } else {
                    console.error(`Error deleting student with ID ${studentId}: ${data.message}`);
                }
            })
            .catch(error => {
                console.error('Error deleting student:', error);
            });
        }
    }
    

    // Function to handle filter button click
    function handleFilterButtonClick() {
        fetchStudents()
            .then(students => {
                if (filterMode === 0) {
                    const sortedByYear = [...students].sort((a, b) => a.student_year.localeCompare(b.student_year));
                    populateStudentsTable(sortedByYear);
                    filterMode = 1;
                } else if (filterMode === 1) {
                    const sortedByProgram = [...students].sort((a, b) => a.student_program.localeCompare(b.student_program));
                    populateStudentsTable(sortedByProgram);
                    filterMode = 2;
                } else {
                    populateStudentsTable(students);
                    filterMode = 0;
                }
            })
            .catch(error => {
                console.error('Error fetching students:', error);
            });
    }
});
