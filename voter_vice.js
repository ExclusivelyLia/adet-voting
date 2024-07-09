window.addEventListener('DOMContentLoaded', (event) => {
    fetch('vp_fetch_candidates.php')
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
                        <input type="radio" id="${candidate.candidate_id}" name="vicepresident" value="${candidate.candidate_id}">
                        <label for="${candidate.candidate_id}"></label> Vice President
                    </div>
                `;
                return candidateElement;
            }

            // Populate Vice President candidates
            const candidatesContainer = document.querySelector('.candidates_container');
            data.forEach(candidate => {
                const candidateElement = createCandidateElement(candidate);
                candidatesContainer.appendChild(candidateElement);
            });
        })
        .catch(error => console.error('Error fetching candidates:', error));
});

document.getElementById('nextButton').addEventListener('click', function() {
    var candidatetSelected = document.querySelector('input[name="vicepresident"]:checked');
    if (!candidatetSelected) {
        alert('Please select a candidate for Vice President.');
        return false;
    } else {
        window.location.href = 'voter_councilor.html';
    }
});

document.getElementById('backButton').addEventListener('click', function() {
    window.location.href = 'voter_president.html';
});

