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
                    <div class="checkbox-container">
                        <input type="checkbox" id="${candidate.candidate_id}" name="councilor" value="${candidate.candidate_id}">
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
    var candidatesSelected = document.querySelectorAll('input[name="councilor"]:checked');
    if (candidatesSelected.length === 0) {
        alert('Please select at least one candidate for Councilor.');
        return false;
    } else {
        // Collect selected candidate IDs
        var selectedIds = Array.from(candidatesSelected).map(checkbox => checkbox.value);
        console.log('Selected candidate IDs:', selectedIds);
        // You can handle the selected candidate IDs here, such as sending them to the server
        window.location.href = 'voter_review.html'; // Set the URL to navigate after selection
    }
});

document.getElementById('backButton').addEventListener('click', function() {
    window.location.href = 'voter_vice.html';
});

