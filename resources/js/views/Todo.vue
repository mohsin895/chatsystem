<template>
    <div class="loyel-erp-branch mt-10 ml-10 mr-10 bg-white p-6">
      <div class="card w-full">
        <div class="p-2 mb-4">
          <div class="bg-customCyan hover:bg-hoverCyan p-2 w-40 cursor-pointer" @click="openModal">
            <span class="inline-flex items-center text-white">
              Add New Todo
              <icon name="plus" size="15px" style="margin-left: 8px" />
            </span>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="border-collapse border border-slate-400 w-full min-w-max">
          <thead>
            <tr>
              <th class="border border-slate-300 h-10">Sr No.</th>
              <th class="border border-slate-300">Title</th>
              <th class="border border-slate-300">Body</th>
              <th class="border border-slate-300">Complete</th>
              <th class="border border-slate-300">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(dataInfo, index) in dataList" :key="dataInfo.id">
              <td class="border border-slate-300 pl-4 h-16 text-sm">{{ index + 1 }}</td>
              <td class="border border-slate-300 pl-4 text-sm">
                {{ dataInfo.title }}
              </td>
              <td class="border border-slate-300 pl-4 text-sm">
               {{dataInfo.body}}
              </td>
              <td class="border border-slate-300 text-center">
            <input type="checkbox" :checked="dataInfo.completed" @change="toggleComplete(dataInfo.id)" />
          </td>


              <td class="border border-slate-300 pl-4">
                <span class="inline-flex items-center">
                  <span class="p-2 cursor-pointer mr-6 inline-flex items-center text-xs bg-purpoleCus hover:bg-purpoleCusHover text-white" @click="openModalEdit(dataInfo)">
                    <icon name="pencil" size="15px" style="margin-right: 10px" />
                    View/Edit
                  </span>
                  <span class="p-2 cursor-pointer inline-flex items-center text-xs bg-red-500 ml-4 hover:bg-red-700 text-white" @click="openModalDelete(dataInfo)">
                    <icon name="trash" size="15px" class="mr-2" />
                    Delete
                  </span>
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
              {{ editMode ? 'Edit Todo' : 'Add New Todo' }}
            </h2>
            <button @click="closeModal" class="text-gray-500 hover:text-gray-700 px-2.5 bg-slate-200">
              &times;
            </button>
          </div>
          <form @submit.prevent="submitForm">
            <div class="pl-6 pr-6 modal-body">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="todo">
                  <span :class="{ 'text-red-500': nameError }">Title</span>
                </label>
                <input
                  v-model="title"
                  type="text"
                  class="appearance-none border w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none"
                  placeholder="Title"
                  :style="{ border: nameError ? '1px solid red' : '1px solid #CED4DA' }"
                  @keyup="formValidation('title')"
                />
                <div class="text-red-500 text-xs mt-1">{{ nameErrorMessage }}</div>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="body">
                  <span :class="{ 'text-red-500': bodyError }">Body</span>
                </label>
                <textarea id="local-body" rows="3"
                 v-model="body"
                  :style="{ border: bodyError ? '1px solid red' : '1px solid #CED4DA' }"
                  @keyup="formValidation('body')"
                  class="py-2 px-3 block w-full border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
               
                <div class="text-red-500 text-xs mt-1">{{ bodyErrorMessage }}</div>
              </div>
             
            </div>
  
            <div class="border-t border-gray-200 modal-footer">
              <div class="p-6 flex items-end justify-end space-x-4">
                <button @click="closeModal" class="bg-white-500 hover:bg-black hover:text-white text-black font-bold py-2 px-4 border border-black focus:outline-none focus:shadow-outline text-right" type="button">
                  Cancel
                </button>
                <button class="bg-customCyan hover:bg-hoverCyan text-white font-bold py-2 px-4 focus:outline-none focus:shadow-outline text-right" type="submit">
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
  
      <!-- Delete Modal -->
      <div v-if="isModalOpenDelete" ref="modalBackdrop" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-start justify-center pt-20" @click="handleBackdropClick">
        <!-- Modal -->
        <div class="bg-white shadow-lg w-full max-w-2xl " @click.stop>
          <div class="flex justify-between items-center p-6 mb-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Confirmation</h2>
            <button @click="closeModal" class="text-gray-500 hover:text-gray-700 px-2.5 bg-slate-200">
              &times;
            </button>
          </div>
          <form @submit.prevent="deleteInfo">
            <div class="modal-body p-6">
              <span>
                Are you sure you want to delete this Todo:
                <span class="font-bold">{{ title }}?</span>
              </span>
              
            </div>
            <div class="border-t border-gray-200">
              <div class="flex items-end justify-end space-x-4 p-6">
                <button @click="closeModal" class="bg-white-500 hover:bg-black hover:text-white text-black font-bold py-2 px-4 border border-black focus:outline-none focus:shadow-outline text-right" type="button">
                  Cancel
                </button>
                <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4  focus:outline-none focus:shadow-outline" type="submit">
                  Delete
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
  <!-- Designation Delete -->
     
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
  const title = ref('');
  const body = ref('');
  const dataList = ref([]);
  const dataId = ref(null);
  const editMode = ref(false);
  const nameError = ref(false);

  const nameErrorMessage = ref('');
  const bodyError = ref(false);

  const bodyErrorMessage = ref('');
  
  function openModal() {
    editMode.value = false;
    isModalOpen.value = true;
  }
  
  function openModalEdit(dataInfo) {
    editMode.value = true;
    dataId.value = dataInfo.id;
    title.value = dataInfo.title;
    body.value = dataInfo.body;
    isModalOpen.value = true;
  }
  
  function openModalDelete(dataInfo) {
    dataId.value = dataInfo.id;
    title.value = dataInfo.title;
    body.value = dataInfo.body;
    isModalOpenDelete.value = true;
  }
  
 
  function closeModal() {
    isModalOpen.value = false;
    isModalOpenDelete.value = false;
    title.value = '';
    body.value=' ';
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
      const response = await axios.get('/todo/get/list', config);
      dataList.value = response.data;
    } catch (error) {
      console.error('Error fetching todo data:', error);
    }
  };
  
  function validateName() {
    if (!title.value || title.value.length === 0) {
      nameError.value = true;
      nameErrorMessage.value = 'Title Field cannot be empty';
      return false;
    } else {
      nameError.value = false;
      nameErrorMessage.value = '';
      return true;
    }
  }
  function validateBody() {
    if (!body.value || body.value.length === 0) {
      bodyError.value = true;
      bodyErrorMessage.value = 'Body Field cannot be empty';
      return false;
    } else {
      bodyError.value = false;
      bodyErrorMessage.value = '';
      return true;
    }
  }
 
  function formValidation(fieldName) {
    if (fieldName === 'submit') {
      const isNameValid = validateName();
      const isBodyValid = validateBody();
      return isNameValid && isBodyValid;
    } else if (fieldName === 'title') {
      validateName();
    } else if (fieldName === 'body') {
      validateBody();
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
        const payload = {
          title: title.value,
          body: body.value,
          dataId: dataId.value || null
        };
        const response = await axios.post('/todo/insert/update', payload, config);
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

  async function toggleComplete(id) {
  try {
    const token = window.localStorage.getItem('token');
    const config = {
      headers: {
        Authorization: 'Bearer ' + token,
      }
    };

    const response = await axios.post('/todo/toggle-complete', { id }, config);

    if (response.data.msgFlag) {
      toast.success(response.data.msg);
      await fetchDataList();
    } else {
      toast.error('Failed to update completion status.');
    }
  } catch (error) {
    console.error('Error toggling task completion:', error);
    toast.error('An error occurred.');
  }
}

  async function deleteInfo() {
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
      const payload = {
        dataId: dataId.value || null
      };
      const response = await axios.post('/todo/delete', payload, config);
      if (response.data.msgFlag) {
        toast.success(response.data.msg);
        await fetchDataList();
        closeModal();
      } else {
        toast.error(response.data.errMsg);
      }
    } catch (error) {
      console.error('Error deleting Branch:', error);
      toast.error('An error occurred while deleting the Branch.');
    }
  }
  
  

  onMounted(fetchDataList);
  </script>
  
  <style scoped>
  .helper-text-product-add {
    color: red;
  }
  </style>
  