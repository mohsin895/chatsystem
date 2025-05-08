<template>
    <div class="loyel-erp-branch mt-10 ml-10 mr-10 bg-white p-6">
      <div class="card w-full">
        <div class="p-2 mb-4">
       
        </div>
        <div class="overflow-x-auto">
          <table class="border-collapse border border-slate-400 w-full min-w-max">
          <thead>
            <tr>
              <th class="border border-slate-300 h-10">Sr No.</th>
              <th class="border border-slate-300">Subject</th>
              <th class="border border-slate-300">Category</th>
              <th class="border border-slate-300">Priority</th>
              <th class="border border-slate-300">Status</th>
              <th class="border border-slate-300">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(dataInfo, index) in dataList" :key="dataInfo.id">
              <td class="border border-slate-300 pl-4 h-16 text-sm">{{ index + 1 }}</td>
              <td class="border border-slate-300 pl-4 text-sm">
                {{ dataInfo.subject }}
              </td>
              <td class="border border-slate-300 pl-4 text-sm">
               {{dataInfo.category}}
              </td>
              <td class="border border-slate-300 pl-4 text-sm">
               {{dataInfo.priority}}
              </td>
              <td class="border border-slate-300 pl-4 text-sm">
               {{dataInfo.status}}
              </td>

              <td class="border border-slate-300 pl-4">
                <span class="inline-flex items-center">
                <span class="p-2 cursor-pointer mr-6 inline-flex items-center text-xs bg-purpoleCus hover:bg-purpoleCusHover text-white" @click="openModalEdit(dataInfo)">
                    <icon name="pencil" size="15px" style="margin-right: 10px" />
                    Update Status
                  </span>
                  <router-link :to="{name:'TicketView', params:{dataId:dataInfo.id}}">
                    <span class="p-2 cursor-pointer mr-6 inline-flex items-center text-xs bg-purpoleCus hover:bg-purpoleCusHover text-white" @click="openModalEdit(dataInfo)">
                    <icon name="eye" size="15px" style="margin-right: 10px" />
                    View/Reply
                  </span>
                  </router-link>
                
                 
                </span>
              </td>
            </tr>
          </tbody>
        </table>
        </div>
      </div>
  
      <!-- Modal Backdrop -->
      <div v-if="isModalOpen" ref="modalBackdrop" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-start max-h-screen overflow-y-auto justify-center pt-20" @click="handleBackdropClick">
        <!-- Modal -->
        <div class="bg-white shadow-lg w-full max-w-lg " @click.stop>
          <div class="flex justify-between items-center p-6 mb-4 border-b border-gray-200 modal-header">
            <h2 class="text-xl inline-flex items-center font-semibold">
              <span v-if="editMode"> <icon name="pencil" size="15px" style="margin-right: 8px" /></span><span v-else> <icon name="plus" size="15px" style="margin-right: 8px" /></span>
              {{ editMode ? 'Edit Ticket' : 'Add New Ticket' }}
            </h2>
            <button @click="closeModal" class="text-gray-500 cursor-pointer hover:text-gray-700 px-2.5 bg-slate-200">
              &times;
            </button>
          </div>
          <form @submit.prevent="submitForm">
            <div class="pl-6 pr-6 modal-body">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="todo">
                  <span :class="{ 'text-red-500': nameError }">Subject</span>
                </label>
              {{subject}}
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="body">
                  <span :class="{ 'text-red-500': bodyError }">Description</span>
                </label>
               {{description}}
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="todo">
                  <span :class="{ 'text-red-500': nameError }">Category:   {{category}}</span>
                </label>
            
              
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="todo">
                  <span :class="{ 'text-red-500': nameError }">Priority:      {{priority}}</span>
                </label>
       
          
              </div>
              
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="todo">
                  <span >Status</span>
                </label>
                <select
              v-model="status"
              
              class="block h-10 pl-2 w-full border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6"
            >
             
              <option value="open">
              Open
              </option>
              <option value="inprogress">
               In Progress
              </option>
              <option value="resolved">
               Resolved
              </option>
              <option value="closed">
               Closed
              </option>
            </select>
             
              </div>

             
            </div>
  
            <div class="border-t border-gray-200 modal-footer">
              <div class="p-6 flex items-end justify-end space-x-4">
                <button @click="closeModal" class="bg-white-500 cursor-pointer hover:bg-black hover:text-white text-black font-bold py-2 px-4 border border-black focus:outline-none focus:shadow-outline text-right" type="button">
                  Cancel
                </button>
                <button class="bg-customCyan hover:bg-hoverCyan cursor-pointer text-white font-bold py-2 px-4 focus:outline-none focus:shadow-outline text-right" type="submit">
                  <span class="inline-flex items-center text-white">
                    <span v-if="editMode"> <icon name="pencil" size="15px" style="margin-right: 8px" /></span><span v-else> <icon name="check" size="15px" style="margin-right: 8px" /></span>
                   
                    {{ editMode ? 'Update' : 'Submit' }}
                  </span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

     
    </div>
  </template>
  
  <script setup>
  import axios from 'axios';
  import { ref, onMounted } from 'vue';
  import { useToast } from 'vue-toastification';
  
  const toast = useToast();
  const isModalOpen = ref(false);
  const isModalOpenDelete = ref(false);
  const modalBackdrop = ref(null);
  const subject = ref('');
  const status = ref('');
  const description = ref('');
  const category = ref('');
  const priority = ref('');

  const dataList = ref([]);
  const dataId = ref(null);
  const editMode = ref(false);
  const nameError = ref(false);

  const nameErrorMessage = ref('');



  
  function openModal() {
    editMode.value = false;
    isModalOpen.value = true;
  }
  
  function openModalEdit(dataInfo) {
    editMode.value = true;
    dataId.value = dataInfo.id;
    subject.value = dataInfo.subject;
    description.value = dataInfo.description;
    category.value = dataInfo.category;
    priority.value = dataInfo.priority;
    status.value = dataInfo.status;
    isModalOpen.value = true;
  }
  
 
 
  function closeModal() {
    isModalOpen.value = false;
    isModalOpenDelete.value = false;
 
    status.value = '';
 
    dataId.value = null;
    editMode.value = false;
    nameError.value = false;
   
    nameErrorMessage.value = '';
  }
  
 
  function handleBackdropClick(event) {
    if (event.target === modalBackdrop.value) {
      closeModal();
    }
  }
 
  const fetchDataList = async () => {
    try {
      const token = window.localStorage.getItem('token');
      if (!token) {
        console.error('Authentication token is missing.');
        return;
      }
      const config = {
        headers: {
          Authorization: 'Bearer ' + token
        }
      };
      const response = await axios.get('/admin/ticket/get/list', config);
      dataList.value = response.data;
    } catch (error) {
      console.error('Error fetching todo data:', error);
    }
  };
  
  function validateName() {
    if (!status.value || status.value.length === 0) {
      nameError.value = true;
      nameErrorMessage.value = 'Status Field cannot be empty';
      return false;
    } else {
      nameError.value = false;
      nameErrorMessage.value = '';
      return true;
    }
  }

 
  function formValidation(fieldName) {
    if (fieldName === 'submit') {
      const isNameValid = validateName();
     
      return isNameValid ;
    } else if (fieldName === 'status') {
      validateName();
    } 
  }
  
  async function submitForm() {
   
    if (formValidation('submit') ) {
  
      try {
        const token = window.localStorage.getItem('token');
        if (!token) {
          console.error('Authentication token is missing.');
          return;
        }
        const config = {
          headers: {
            Authorization: 'Bearer ' + token
          }
        };
       
        const formData = new FormData();
      
        formData.append("status", status.value);
        formData.append("dataId", dataId.value || null);
    
        const response = await axios.post('admin/ticket/status', formData, config);


        if (response.data.msgFlag) {
          toast.success(response.data.msg);
          await fetchDataList();
          closeModal();
        } else {
          toast.error(response.data.errMsg);
        }
      } catch (error) {
        console.error('Error submitting form:', error);
        toast.error('An error occurred while submitting the form.');
      }
    }
  }



  onMounted(fetchDataList);
  </script>
  
  <style scoped>
  .helper-text-product-add {
    color: red;
  }
  </style>
  