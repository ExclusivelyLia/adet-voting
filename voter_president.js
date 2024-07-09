window.addEventListener('DOMContentLoaded', (event) => {
    fetch('p_fetch_candidates.php')
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
                        <div class="candidate-info">
                            <p class="name">${candidate.candidate_fname} ${candidate.candidate_lname}</p>
                            <p class="party">Party List: ${candidate.party_list}</p>
                        </div>
                    </div>
                    <div class="radio-container">
                        <input type="radio" id="${candidate.candidate_id}" name="president" value="${candidate.candidate_id}">
                        <label for="${candidate.candidate_id}"></label> President
                    </div>
                `;
                return candidateElement;
            }

            // Populate President candidates
            const candidateContainer = document.querySelector('.candidates_container');
            data.forEach(candidate => {
                const candidateElement = createCandidateElement(candidate);
                candidateContainer.appendChild(candidateElement);
            });
        })
        .catch(error => console.error('Error fetching candidates:', error));
});

document.getElementById('nextButton').addEventListener('click', function() {
    var presidentSelected = document.querySelector('input[name="president"]:checked');
    if (!presidentSelected) {
        alert('Please select a candidate for President.');
        return false;
    } else {
        window.location.href = 'voter_vice.html';
    }
});

document.getElementById('backButton').addEventListener('click', function() {
    window.location.href = 'voter_rules.html';
});
