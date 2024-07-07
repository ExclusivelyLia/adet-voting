let filterMode = 0;

// Function to fetch and display candidates from the server
function fetchCandidates() {
    return fetch('candidate_record.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();  
        })
        .catch(error => {
            console.error('Error fetching candidates:', error);
            throw error;  // Propagate the error to the caller
        });
}

// Function to fetch position names from the server
function fetchPositionNames() {
    return fetch('position_record.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();  
        })
        .catch(error => {
            console.error('Error fetching position names:', error);
            throw error;  // Propagate the error to the caller
        });
}

// Function to populate candidates table with position names
function populateCandidatesTable(candidates, positions) {
    const tableBody = document.getElementById("candidatesTableBody");
    tableBody.innerHTML = "";

    candidates.forEach(candidate => {
        let row = "<tr>";
        row += `<td>${candidate.candidate_id}</td>`;
        
        // Find position_name using position_id from positions array
        const position = positions.find(pos => pos.position_id === candidate.position_id);
        const positionName = position ? position.position_name : 'Unknown';

        row += `<td>${positionName}</td>`;
        row += `<td>${candidate.candidate_fname}</td>`;
        row += `<td>${candidate.candidate_mname}</td>`;
        row += `<td>${candidate.candidate_lname}</td>`;
        row += `<td>${candidate.party_list}</td>`;
        row += `<td>Actions Placeholder</td>`;
        row += "</tr>";
        tableBody.innerHTML += row;
    });
}

// Function to filter and sort candidates by position
function filterAndSortCandidatesByPosition(candidates, positions) {
    const positionPriority = {
        'President': 1,
        'Vice President': 2,
        'Councilor': 3
    };

    const sortedCandidates = [...candidates]; // Create a copy to preserve original data
    sortedCandidates.sort((a, b) => {
        const positionA = positions.find(pos => pos.position_id === a.position_id);
        const positionB = positions.find(pos => pos.position_id === b.position_id);
        const positionNameA = positionA ? positionA.position_name : '';
        const positionNameB = positionB ? positionB.position_name : '';

        const priorityA = positionPriority[positionNameA] || 999;
        const priorityB = positionPriority[positionNameB] || 999;

        return priorityA - priorityB;
    });

    return sortedCandidates;
}

// Function to handle filter button click
function handleFilterButtonClick() {
    fetchCandidates()
        .then(candidates => {
            return fetchPositionNames()
                .then(positions => {
                    if (filterMode === 0) {
                        const sortedByPosition = filterAndSortCandidatesByPosition(candidates, positions);
                        populateCandidatesTable(sortedByPosition, positions);
                        filterMode = 1;
                    } else if (filterMode === 1) {
                        const sortedByPartyList = [...candidates].sort((a, b) => a.party_list.localeCompare(b.party_list));
                        populateCandidatesTable(sortedByPartyList, positions);
                        filterMode = 2;
                    } else {
                        populateCandidatesTable(candidates, positions);
                        filterMode = 0;
                    }
                });
        })
        .catch(error => {
            console.error('Error fetching candidates or positions:', error);
        });
}

// Event listener for input change on search
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const candidatesTableBody = document.getElementById('candidatesTableBody');

    if (searchInput && candidatesTableBody) {
        searchInput.addEventListener('input', function() {
            const searchTerm = searchInput.value.toLowerCase().trim();

            fetchCandidates()
                .then(candidates => {
                    const filteredCandidates = candidates.filter(candidate =>
                        candidate.candidate_id.toLowerCase().includes(searchTerm) ||
                        candidate.candidate_fname.toLowerCase().includes(searchTerm) ||
                        candidate.candidate_mname.toLowerCase().includes(searchTerm) ||
                        candidate.candidate_lname.toLowerCase().includes(searchTerm) ||
                        candidate.party_list.toLowerCase().includes(searchTerm)
                    );
                    // Fetch position names and then populate table
                    fetchPositionNames()
                        .then(positions => {
                            populateCandidatesTable(filteredCandidates, positions);
                        })
                        .catch(error => {
                            console.error('Error fetching position names:', error);
                        });
                })
                .catch(error => {
                    console.error('Error fetching or filtering candidates:', error);
                });
        });

        // Initial fetch and populate
        fetchCandidates()
            .then(candidates => {
                fetchPositionNames()
                    .then(positions => {
                        populateCandidatesTable(candidates, positions);
                    })
                    .catch(error => {
                        console.error('Error fetching position names:', error);
                    });
            })
            .catch(error => {
                console.error('Error fetching initial candidates:', error);
            });
    } else {
        console.error('Required elements (searchInput or candidatesTableBody) not found in the DOM.');
    }
});

// Event listener for filter button (sort by position)
document.addEventListener('DOMContentLoaded', function() {
    const filterButton = document.querySelector('.filter');
    if (filterButton) {
        filterButton.addEventListener('click', handleFilterButtonClick);
    } else {
        console.error('Filter button not found in the DOM.');
    }
});

// Event listener for voting-time button (for demonstration purposes)
document.querySelector('.voting-time').addEventListener('click', function() {
    alert('Calendar would pop up here');
});
