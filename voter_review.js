document.addEventListener('DOMContentLoaded', (event) => {
    // Function to create candidate elements
    function createCandidateElement(candidate) {
        const candidateElement = document.createElement('div');
        candidateElement.classList.add('outer-box');
        candidateElement.innerHTML = `
            <div class="inner-box">
                <div class="candidate-img">
                    <img src="${candidate.candidate_img || 'css/pictures/default-ppic.png'}" alt="Candidate Image" onerror="this.src='css/pictures/default-ppic.png';">
                </div>
                <div class="candidate-info">
                    <p class="name">${candidate.candidate_fname} ${candidate.candidate_lname}</p>
                    <p class="party">Party List: ${candidate.party_list}</p>
                </div>
            </div>
        `;
        return candidateElement;
    }

    // Function to display selected candidates
    function displaySelectedCandidates(selectedCandidates) {
        const presidentContainer = document.querySelector('.president_container');
        const vicePresidentContainer = document.querySelector('.vice_president_container');
        const councilorsContainer = document.querySelector('.councilors_container');

        // Clear previous selections
        presidentContainer.innerHTML = '';
        vicePresidentContainer.innerHTML = '';
        councilorsContainer.innerHTML = '';

        // Populate President
        if (selectedCandidates.president) {
            const presidentElement = createCandidateElement(selectedCandidates.president);
            presidentContainer.appendChild(presidentElement);
        } else {
            presidentContainer.innerHTML = '<p>No President selected</p>';
        }

        // Populate Vice President
        if (selectedCandidates.vice_president) {
            const vicePresidentElement = createCandidateElement(selectedCandidates.vice_president);
            vicePresidentContainer.appendChild(vicePresidentElement);
        } else {
            vicePresidentContainer.innerHTML = '<p>No Vice President selected</p>';
        }

        // Populate Councilors
        if (selectedCandidates.councilors && selectedCandidates.councilors.length > 0) {
            selectedCandidates.councilors.forEach(councilor => {
                const councilorElement = createCandidateElement(councilor);
                councilorsContainer.appendChild(councilorElement);
            });
        } else {
            councilorsContainer.innerHTML = '<p>No Councilors selected</p>';
        }
    }

    // Load selected candidates from localStorage
    const selectedPresident = JSON.parse(localStorage.getItem('selectedPresident'));
    const selectedVicePresident = JSON.parse(localStorage.getItem('selectedVicePresident'));
    const selectedCouncilors = JSON.parse(localStorage.getItem('selectedCouncilors')) || [];

    console.log('Selected President:', selectedPresident);
    console.log('Selected Vice President:', selectedVicePresident);
    console.log('Selected Councilors:', selectedCouncilors);

    // Fetch selected candidates from the server
    const selectedPresidentId = selectedPresident ? selectedPresident.id : null;
    const selectedVicePresidentId = selectedVicePresident ? selectedVicePresident.id : null;
    const selectedCouncilorIds = selectedCouncilors.map(c => c.id);

    const queryParams = new URLSearchParams({
        selectedPresidentId: selectedPresident ? selectedPresidentId : '',
        selectedVicePresidentId: selectedVicePresident ? selectedVicePresidentId : '',
        selectedCouncilorIds: JSON.stringify(selectedCouncilorIds)
    });

    fetch(`fetch_candidates.php?${queryParams}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Fetched Candidates:', data);
            displaySelectedCandidates(data);
        })
        .catch(error => {
            console.error('Error fetching selected candidates:', error);
            alert('An error occurred while fetching candidates. Please try again.');
        });

    // Submit Button Event
    document.getElementById('submitButton').addEventListener('click', function() {
        console.log('Submit button clicked');

        fetch('submit_vote.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                presidentVote: selectedPresident ? selectedPresident.id : null,
                vicePresidentVote: selectedVicePresident ? selectedVicePresident.id : null,
                councilorVotes: selectedCouncilors.map(c => c.id)
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Response:', data);
            if (data.success) {
                // Redirect to submitted alert page
                window.location.href = 'submitted_alert.html';
            } else {
                alert('Error: ' + (data.message || 'Unknown error occurred'));
            }
        })
        .catch(error => {
            console.error('Error submitting vote:', error);
            alert('An error occurred while submitting your vote: ' + error.message);
        });
    });

    // Back Button Event
    document.getElementById('backButton').addEventListener('click', function() {
        window.location.href = 'voter_councilor.html';
    });
});