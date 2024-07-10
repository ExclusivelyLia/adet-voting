document.addEventListener('DOMContentLoaded', (event) => {
    // Function to create candidate elements
    function createCandidateElement(candidate) {
        const candidateElement = document.createElement('div');
        candidateElement.classList.add('outer-box');
        candidateElement.innerHTML = `
            <div class="inner-box">
                <div class="candidate-img">
                    <img src='${candidate.candidate_img ? candidate.candidate_img : 'css/pictures/default-ppic.png'}' alt="Candidate Image">
                </div>
                <div class="candidate-info">
                    <p class="name">${candidate.name}</p>
                    <p class="party">Party List: ${candidate.party}</p>
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
            console.warn('No President selected');
        }

        // Populate Vice President
        if (selectedCandidates.vicePresident) {
            const vicePresidentElement = createCandidateElement(selectedCandidates.vicePresident);
            vicePresidentContainer.appendChild(vicePresidentElement);
        } else {
            console.warn('No Vice President selected');
        }

        // Populate Councilors
        if (selectedCandidates.councilors && selectedCandidates.councilors.length > 0) {
            selectedCandidates.councilors.forEach(councilor => {
                const councilorElement = createCandidateElement(councilor);
                councilorsContainer.appendChild(councilorElement);
            });
        } else {
            console.warn('No Councilors selected');
        }
    }

    // Load selected candidates from localStorage
    const selectedPresident = JSON.parse(localStorage.getItem('selectedPresident'));
    const selectedVicePresident = JSON.parse(localStorage.getItem('selectedVicePresident'));
    const selectedCouncilors = JSON.parse(localStorage.getItem('selectedCouncilors'));

    console.log('Selected President:', selectedPresident);
    console.log('Selected Vice President:', selectedVicePresident);
    console.log('Selected Councilors:', selectedCouncilors);

    // Display selected candidates on page load
    displaySelectedCandidates({
        president: selectedPresident,
        vicePresident: selectedVicePresident,
        councilors: selectedCouncilors
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
                councilorVotes: selectedCouncilors ? selectedCouncilors.map(c => c.id) : []
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response:', data);
            if (data.success) {
                // Update localStorage with selected candidates after voting
                localStorage.setItem('selectedPresident', JSON.stringify(selectedPresident));
                localStorage.setItem('selectedVicePresident', JSON.stringify(selectedVicePresident));
                localStorage.setItem('selectedCouncilors', JSON.stringify(selectedCouncilors));

                // Display selected candidates on successful submission
                displaySelectedCandidates(data.selectedCandidates);

                // Redirect to submitted alert page
                window.location.href = 'submitted_alert.html';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error submitting vote:', error);
            alert('An error occurred while submitting your vote. Please try again.');
        });
    });

    // Back Button Event
    document.getElementById('backButton').addEventListener('click', function() {
        window.location.href = 'voter_councilor.html';
    });
});
