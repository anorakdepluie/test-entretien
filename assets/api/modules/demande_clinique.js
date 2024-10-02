import axios from 'axios';

export default {
  depots: {
    all: async () => (await axios.get('/v1/demande-clinique/depots')).data,
    ajouterReponse: async (id, titre, description, type) => (await axios.post(`/v1/demande-clinique/depots/${id}/reponses`, {titre, description, type})).data,
    validerReponse: async (depot, id, motifValidation) => (await axios.post(`/v1/demande-clinique/depots/${depot}/reponses/${id}/validation`, {motifValidation})).data
  },
  reponses: {
    creer: async (depot) => (await axios.post('/v1/demande-clinique/reponses', depot)).data,
  }
};