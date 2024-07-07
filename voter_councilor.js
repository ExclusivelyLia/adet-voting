window.addEventListener('DOMContentLoaded', (event) => {
    fetch('c_fetch_candidates.php')
        .then(response => response.json())
        .then(data => {
            // Function to create candidate element
            function createCandidateElement(candidate) {
                const candidateElement = document.createElement('div');
                candidateElement.classList.add('outer-box');
                candidateElement.innerHTML = `
                    <div class="inner-box">
                        <div class="candidate-img">
                            <img src='${candidate.candidate_img ? candidate.candidate_img : 'css/pictures/ppic.jpg'}' alt="Candidate Image">
                        </div>
                        <div class="candidate">
                            <div class="candidate-info">
                                <p class="name">${candidate.candidate_fname} ${candidate.candidate_lname}</p>
                                <p class="party">Party List: ${candidate.party_list}</p>
                            </div>
                        </div>
                    </div>
                    <div class="radio-container">
                        <input type="radio" id="${candidate.candidate_id}" name="councilor" value="${candidate.candidate_id}">
                        <label for="${candidate.candidate_id}"></label> Councilor
                    </div>
                `;
                return candidateElement;
            }

            // Populate Councilor candidates
            const candidateContainer = document.querySelector('.candidates_container');
            data.forEach(candidate => {
                const candidateElement = createCandidateElement(candidate);
                candidateContainer.appendChild(candidateElement);
            });
        })
        .catch(error => console.error('Error fetching candidates:', error));
});

document.getElementById('nextButton').addEventListener('click', function() {
    var candidateSelected = document.querySelector('input[name="councilor"]:checked');
    if (!candidateSelected) {
        alert('Please select candidates for Councilor.');
        return false;
    } else {
        window.location.href = '';
    }
});


