window.addEventListener('DOMContentLoaded', (event) => {
    fetch('c_fetch_candidates.php')
        .then(response => response.json())
        .then(data => {
            console.log('Fetched Councilor Candidates:', data); // Log fetched data
            
            function createCandidateElement(candidate) {
                const candidateElement = document.createElement('div');
                candidateElement.classList.add('outer-box');

                const innerHTML = `
                    <div class="inner-box">
                        <div class="candidate-img">
                            <img src='${candidate.candidate_img ? candidate.candidate_img : 'css/pictures/default-ppic.png'}' alt="Candidate Image">
                        </div>
                        <div class="candidate-info">
                            <p class="name">${candidate.candidate_fname} ${candidate.candidate_lname}</p>
                            <p class="party">Party List: ${candidate.party_list}</p>
                        </div>
                    </div>
                    <div class="checkbox-container">
                        <input type="checkbox" id="${candidate.candidate_id}" name="councilors" value="${candidate.candidate_id}" data-name="${candidate.candidate_fname} ${candidate.candidate_lname}" data-party="${candidate.party_list}">
                        <label for="${candidate.candidate_id}"></label> Councilor
                    </div>
                `;
                
                candidateElement.innerHTML = innerHTML;
                return candidateElement;
            }

            const candidateContainer = document.querySelector('.candidates_container');
            data.forEach(candidate => {
                const candidateElement = createCandidateElement(candidate);
                candidateContainer.appendChild(candidateElement);
            });

            // Limiting selection to a maximum of 6 candidates
            const councilorCheckboxes = document.querySelectorAll('input[name="councilors"]');
            councilorCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    let selectedCount = document.querySelectorAll('input[name="councilors"]:checked').length;
                    if (selectedCount > 6) {
                        this.checked = false; // Prevent checking more than 6
                        alert('You can select up to 6 candidates for Councilor.');
                    }
                });
            });
        })
        .catch(error => console.error('Error fetching candidates:', error));
});

document.getElementById('nextButton').addEventListener('click', function() {
    var selectedCandidates = document.querySelectorAll('input[name="councilors"]:checked');
    if (selectedCandidates.length === 0) {
        alert('Please select at least one candidate for Councilor.');
        return false;
    } else if (selectedCandidates.length > 6) {
        alert('You can select up to 6 candidates for Councilor.');
        return false;
    } else {
        const selectedCouncilors = Array.from(selectedCandidates).map(candidate => ({
            id: candidate.value,
            name: candidate.getAttribute('data-name'),
            party: candidate.getAttribute('data-party'),
            candidate_img: candidate.getAttribute('data-img')
        }));
        console.log('Selected Councilors:', selectedCouncilors); // Log selected councilors
        localStorage.setItem('selectedCouncilors', JSON.stringify(selectedCouncilors));
        window.location.href = 'voter_review.html';
    }
});

document.getElementById('backButton').addEventListener('click', function() {
    window.location.href = 'voter_vice.html';
});
