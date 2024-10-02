<template>
  <div>
    <div class="mb-2">
      <h1 class="text-4xl font-bold">Bienvenue sur ma belle application</h1>
      <p class="text-xl">Listing des demandes cliniques</p>
    </div>
    <div class="flex gap-2 flex-col w-full">
      <div 
        v-for="depot in depots"
        :key="depot.id"
        class="bg-white rounded-xl shadow-sm p-4"
      >
        <p class="text-base font-semibold">Titre: <span class="text-base text-gray-700 font-light">{{ depot.titre }}</span></p>
        <p class="text-base font-semibold">Description: <span class="text-base text-gray-700 font-light">{{ depot.description }}</span></p>
        <p class="text-base font-semibold">Date de création: <span class="text-base text-gray-700 font-light">{{ depot.date_creation }}</span></p>
        <div class="my-4 p-2 border border-gray rounded-xl bg-gray-100 flex flex-col gap-2" v-if="depot.reponses.length">
          <div class="border border-dashed border-2 bg-white px-4 py-2" :class="[reponse.valide ? 'border-green-500' : 'cursor-pointer', isReponseSelected(reponse) ? 'border-blue-500' : 'border-gray-300']" 
            v-for="reponse in depot.reponses" :key="reponse.id" @click="selectReponse(reponse)">
            <div class="flex justify-between items-center">
              <div class="flex flex-col gap-2">
                <p class="text-base font-semibold text-red-500">Type: <span class="text-base text-gray-700 font-light">{{ getTypeLabel(reponse.type) }}</span></p>
                <p class="text-base font-semibold">Titre: <span class="text-base text-gray-700 font-light">{{ reponse.titre }}</span></p>
                <p class="text-base font-semibold">Description: <span class="text-base text-gray-700 font-light">{{ reponse.description }}</span></p>
                <p class="text-base font-semibold">Date de création: <span class="text-base text-gray-700 font-light">{{ reponse.date_creation }}</span></p>
              </div>
              <div v-if="reponse.valide">
                <p class="text-base font-semibold text-green-500">Réponse validée</p>
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-center" v-else>
          <p class="text-base font-semibold">Aucune réponse</p>
        </div>

        <div class="flex justify-between">
          <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2" @click="$router.push(`/depots/${depot.id}`)">Répondre à la demande</button>
  
          <div v-if="depotIdSelectionReponses == depot.id" class="flex gap-2">
            <input v-model="motifValidation" type="text" placeholder="Motif de validation" class="border border-gray-300 rounded-lg p-2 mt-2">
            <button class="text-white font-bold py-2 px-4 rounded mt-2" :class="[motifValidation ? 'bg-green-500 hover:bg-green-700' : 'bg-gray-500']"
              :title="!motifValidation ? 'Veuillez entrer un motif de validation' : ''"
              @click="validerReponse(depot)" :disabled="!motifValidation || loading">
              <span v-if="loading">Validation en cours...</span>
              <span v-else-if="selectionReponses.length > 1">Valider les {{ selectionReponses.length }} réponses</span>
              <span v-else>Valider la réponse</span>
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import { getLabel } from '@/enum/demande_clinique/reponse/type';
import api from '@/api';


export default {
  name: 'Index',
  data() {
    return {
      selectionReponses: [],
      motifValidation: '',
      loading: false,
    };
  },
  computed: {
    ...mapGetters({
      depots: 'demande_clinique/depots',
    }),
    depotIdSelectionReponses() {
      return this.selectionReponses.length ? this.selectionReponses[0].depot : null;
    }
  },
  methods: {
    ...mapActions({
      chargerDepots: 'demande_clinique/chargerDepots',
    }),
    getTypeLabel: getLabel,
    selectReponse(reponse) {
      if (reponse.valide) return;

      if (this.selectionReponses.includes(reponse)) {
        this.selectionReponses = this.selectionReponses.filter(r => r.id !== reponse.id);
      } else {
        this.selectionReponses = this.selectionReponses.filter(r => r.depot === reponse.depot);
        this.selectionReponses.push(reponse);
      }
      this.motifValidation = '';
    },
    isReponseSelected(reponse) {
      return this.selectionReponses.includes(reponse);
    },
    async validerReponse(depot) {
      try {
        this.loading = true;
        for (const reponse of this.selectionReponses) {
          await api.demande_clinique.depots.validerReponse(depot.id, reponse.id, this.motifValidation);
        }
        await this.chargerDepots();
        this.selectionReponses = [];
        this.motifValidation = '';
      } catch (error) {
        console.error('Error validation responses:', error);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>