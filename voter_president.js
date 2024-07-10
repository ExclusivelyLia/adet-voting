window.addEventListener('DOMContentLoaded', (event) => {
    fetch('p_fetch_candidates.php')
        .then(response => response.json())
        .then(data => {
            console.log('Fetched President Candidates:', data); // Log fetched data
            function createCandidateElement(candidate) {
                const candidateElement = document.createElement('div');
                candidateElement.classList.add('outer-box');
                candidateElement.innerHTML = `
                    <div class="inner-box">
                        <div class="candidate-img">
                            <img src='${candidate.candidate_img ? candidate.candidate_img : 'css/pictures/default-ppic.png'}' alt="Candidate Image">
                        </div>
                        <div class="candidate-info">
                            <p class="name">${candidate.candidate_fname} ${candidate.candidate_lname}</p>
                            <p class="party">Party List: ${candidate.party_list}</p>
                        </div>
                    </div>
                    <div class="radio-container">
                        <input type="radio" id="${candidate.candidate_id}" name="president" value="${candidate.candidate_id}" data-name="${candidate.candidate_fname} ${candidate.candidate_lname}" data-party="${candidate.party_list}">
                        <label for="${candidate.candidate_id}"></label> President
                    </div>
                `;
                return candidateElement;
            }

            const candidateContainer = document.querySelector('.candidates_container');
            data.forEach(candidate => {
                const candidateElement = createCandidateElement(candidate);
                candidateContainer.appendChild(candidateElement);
            });
        })
        .catch(error => console.error('Error fetching candidates:', error));
});

document.getElementById('nextButton').addEventListener('click', function() {
    var candidateSelected = document.querySelector('input[name="president"]:checked');
    if (!candidateSelected) {
        alert('Please select a candidate for President.');
        return false;
    } else {
        const selectedPresident = {
            id: candidateSelected.value,
            name: candidateSelected.getAttribute('data-name'),
            party: candidateSelected.getAttribute('data-party'),
            candidate_img: candidateSelected.getAttribute('data-img')
        };
        console.log('Selected President:', selectedPresident); // Log selected president
        localStorage.setItem('selectedPresident', JSON.stringify(selectedPresident));
        window.location.href = 'voter_vice.html';
    }
});

document.getElementById('backButton').addEventListener('click', function() {
    window.location.href = 'voter_rules.html';
});
