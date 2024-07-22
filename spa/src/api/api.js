const ENV_API_ENDPOINT = process.env.API_ENDPOINT;

function fetchCollection(path) {
  return fetch(ENV_API_ENDPOINT + path)
    .then(resp => resp.json())
    .then(json => json['hydra:member'])
    .catch(error => {
      console.error('Error fetching data:', error);
      throw error;
    });
}

export function findConferences() {
  return fetchCollection('api/conferences');
}

export function findComments(conference) {
  return fetchCollection('api/comments?conference='+conference.id);
}





