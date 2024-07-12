document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const candidatesTableBody = document.getElementById('candidatesTableBody');
    const filterButton = document.querySelector('.filter');
    const datePickerInput = document.getElementById('date-picker-input');
    const votingEndButton = document.getElementById('votingEnd');
    const flatpickrContainer = document.getElementById('flatpickr-container');

    let filterMode = 0; // Initialize filterMode here

    // Check for the necessary elements before proceeding
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

    // Filter button event listener
    if (filterButton) {
        filterButton.addEventListener('click', handleFilterButtonClick);
    } else {
        console.error('Filter button not found in the DOM.');
    }

    // Flatpickr initialization
    if (datePickerInput && votingEndButton && flatpickrContainer) {
        const flatpickrInstance = flatpickr(datePickerInput, {
            onChange: function(selectedDates, dateStr, instance) {
                votingEndButton.innerHTML = 
                    '<i class="fa-solid fa-calendar-days" style="color: #e2e2e2;"></i> ' + dateStr;
                setVotingDeadline(dateStr); // Call function to set voting deadline
            },
            appendTo: flatpickrContainer,
            position: "below"
        });

        votingEndButton.addEventListener('click', function() {
            flatpickrInstance.open();
        });
    } else {
        console.error('Date picker elements not found in the DOM.');
    }

    // Function to fetch candidates
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
                throw error;
            });
    }

    // Function to fetch position names
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
                throw error;
            });
    }

    // Function to populate candidates table
    function populateCandidatesTable(candidates, positions) {
        const tableBody = document.getElementById("candidatesTableBody");
        tableBody.innerHTML = "";

        candidates.forEach(candidate => {
            let row = "<tr>";
            row += `<td>${candidate.candidate_id}</td>`;
            
            const position = positions.find(pos => pos.position_id === candidate.position_id);
            const positionName = position ? position.position_name : 'Unknown';

            row += `<td>${positionName}</td>`;
            row += `<td>${candidate.candidate_fname}</td>`;
            row += `<td>${candidate.candidate_mname}</td>`;
            row += `<td>${candidate.candidate_lname}</td>`;
            row += `<td>${candidate.party_list}</td>`;
            row += `<td>
                        <button class="delete-button" data-candidate-id="${candidate.candidate_id}">Delete</button>
                    </td>`;
            row += "</tr>";
            tableBody.innerHTML += row;
        });
    }

    // Function to delete candidate
    function deleteCandidate(candidateId) {
        if (confirm("Are you sure you want to delete this candidate?")) {
            fetch(`candidate_delete.php?id=${candidateId}`, {
                method: 'DELETE',
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log(`Candidate with ID ${candidateId} deleted successfully.`);
                    // Refresh the candidate table
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
                    console.error(`Error deleting candidate with ID ${candidateId}: ${data.message}`);
                }
            })
            .catch(error => {
                console.error('Error deleting candidate:', error);
            });
        }
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

    // Function to filter and sort candidates by position
    function filterAndSortCandidatesByPosition(candidates, positions) {
        const positionPriority = {
            'President': 1,
            'Vice President': 2,
            'Councilor': 3
        };

        const sortedCandidates = [...candidates];
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

    // Function to set voting deadline
    function setVotingDeadline(deadline) {
        console.log('Setting voting deadline to:', deadline); // Debugging log
    
        fetch('set_voting_deadline.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ deadline: deadline })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to set voting deadline');
            }
            return response.json(); // Assuming PHP script returns JSON
        })
        .then(data => {
            if (data.success) {
                console.log('Voting deadline set successfully');
                // Optionally, show a notification to the user
                alert('Voting deadline updated successfully');
            } else {
                console.error('Error setting voting deadline:', data.message);
            }
        })
        .catch(error => {
            console.error('Error setting voting deadline:', error);
        });
    }
    
});