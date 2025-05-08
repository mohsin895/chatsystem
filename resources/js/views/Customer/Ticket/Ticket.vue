<template>
    <div class="loyel-erp-branch mt-10 ml-10 mr-10 bg-white p-6">
      <div class="card w-full">
        <div class="p-2 mb-4">
          <div class="bg-customCyan hover:bg-hoverCyan p-2 w-40 cursor-pointer" @click="openModal">
            <span class="inline-flex items-center text-white">
              Add New Ticket
              <icon name="plus" size="15px" style="margin-left: 8px" />
              <Plush class="mr-2" :size="15" />
            </span>
          </div>
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
                    <Pencil  class="mr-2" :size="15" />
                    Edit
                  </span>
                  <router-link :to="{name:'CustomerTicketView', params:{dataId:dataInfo.id}}">
                    <span class="p-2 cursor-pointer mr-6 inline-flex items-center text-xs bg-purpoleCus hover:bg-purpoleCusHover text-white" @click="openModalEdit(dataInfo)">
                      <Eye class="mr-2" :size="15" />
                    View
                  </span>
                  </router-link>
                
                  <span class="p-2 cursor-pointer inline-flex items-center text-xs bg-red-500 ml-4 hover:bg-red-700 text-white" @click="openModalDelete(dataInfo)">
                    <Trash class="mr-2" :size="15" />
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
      <div v-if="isModalOpen" ref="modalBackdrop" class="fixed inset-0 bg-gray-800 bg-opacity-60 flex items-start max-h-screen overflow-y-auto justify-center pt-20" @click="handleBackdropClick">
        <!-- Modal -->
        <div class="bg-white shadow-lg w-full max-w-lg " @click.stop>
          <div class="flex justify-between items-center p-6 mb-4 border-b border-gray-200 modal-header">
            <h2 class="text-xl inline-flex items-center font-semibold">
              <span v-if="editMode"> <icon name="pencil" size="15px" style="margin-right: 8px" /></span><span v-else> <icon name="plus" size="15px" style="margin-right: 8px" /></span>
              {{ editMode ? 'Edit Ticket' : 'Add New Ticket' }}
            </h2>
            <button @click="closeModal" class="text-gray-500 hover:text-gray-700 px-2.5 bg-slate-200 cursor-pointer">
              &times;
            </button>
          </div>
          <form @submit.prevent="submitForm">
            <div class="pl-6 pr-6 modal-body">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="todo">
                  <span :class="{ 'text-red-500': nameError }">Subject</span>
                </label>
                <input
                  v-model="subject"
                  type="text"
                  class="appearance-none border w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none"
                  placeholder="Subject"
                  :style="{ border: nameError ? '1px solid red' : '1px solid #CED4DA' }"
                  @keyup="formValidation('subject')"
                />
                <div class="text-red-500 text-xs mt-1">{{ nameErrorMessage }}</div>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="body">
                  <span :class="{ 'text-red-500': bodyError }">Description</span>
                </label>
                <textarea id="local-body" rows="3"
                 v-model="description"
                  :style="{ border: bodyError ? '1px solid red' : '1px solid #CED4DA' }"
                  @keyup="formValidation('description')"
                  class="py-2 px-3 block w-full border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
               
                <div class="text-red-500 text-xs mt-1">{{ bodyErrorMessage }}</div>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="todo">
                  <span :class="{ 'text-red-500': nameError }">Category</span>
                </label>
                <select
              v-model="category"
              
              class="block h-10 pl-2 w-full border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6"
            >
             
              <option value="Technical">
                Technical
              </option>
              <option value="Billing">
                Billing
              </option>
              <option value="General">
                General
              </option>
            </select>
                <div class="text-red-500 text-xs mt-1">{{ nameErrorMessage }}</div>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="todo">
                  <span :class="{ 'text-red-500': nameError }">Priority</span>
                </label>
                <select
              v-model="priority"
              
              class="block h-10 pl-2 w-full border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6"
            >
             
              <option value="low">
                Low
              </option>
              <option value="medium">
                Medium
              </option>
              <option value="high">
                High
              </option>
            </select>
                <div class="text-red-500 text-xs mt-1">{{ nameErrorMessage }}</div>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="todo">
                  <span :class="{ 'text-red-500': nameError }">Attachment</span>
                </label>
                <input
                     @change="handleFileChange($event, 'attachement')"
                  type="file"
                accept=".pdf,application/pdf"
                  class="appearance-none border w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none"
                  placeholder="attachment"
                  :style="{ border: nameError ? '1px solid red' : '1px solid #CED4DA' }"
                  @keyup="formValidation('attachment')"
                />
                <div class="text-red-500 text-xs mt-1">{{ nameErrorMessage }}</div>
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
  
      <!-- Delete Modal -->
      <div v-if="isModalOpenDelete" ref="modalBackdrop" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-start justify-center pt-20" @click="handleBackdropClick">
        <!-- Modal -->
        <div class="bg-white shadow-lg w-full max-w-2xl " @click.stop>
          <div class="flex justify-between items-center p-6 mb-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Confirmation</h2>
            <button @click="closeModal" class="text-gray-500 hover:text-gray-700 px-2.5 bg-slate-200 cursor-pointer">
              &times;
            </button>
          </div>
          <form @submit.prevent="deleteInfo">
            <div class="modal-body p-6">
              <span>
                Are you sure you want to delete this Ticket:
                <span class="font-bold">{{ subject }}?</span>
              </span>
              
            </div>
            <div class="border-t border-gray-200">
              <div class="flex items-end justify-end space-x-4 p-6">
                <button @click="closeModal" class="bg-white-500 cursor-pointer hover:bg-black hover:text-white text-black font-bold py-2 px-4 border border-black focus:outline-none focus:shadow-outline text-right" type="button">
                  Cancel
                </button>
                <button class="bg-red-500 hover:bg-red-700 cursor-pointer text-white font-bold py-2 px-4  focus:outline-none focus:shadow-outline" type="submit">
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
  import { 
  Trash as Trash,
  Plus  as Plush,
  Pencil  as Pencil ,
  Eye as Eye,
  Check as CheckIcon,
  CheckCheck as CheckCheckIcon,
  Clock as ClockIcon,
  Paperclip as PaperclipIcon,
  Smile as SmileIcon,
  Image as ImageIcon,
  Send as SendIcon,
  Settings as SettingsIcon,
  Menu as MenuIcon,
  MessageSquare as MessageSquareIcon
} from 'lucide-vue-next';

  
  const toast = useToast();
  const isModalOpen = ref(false);
  const isModalOpenDelete = ref(false);
  const modalBackdrop = ref(null);
  const subject = ref('');
  const description = ref('');
  const category = ref('');
  const priority = ref('');
  const attachement = ref('');
  const dataList = ref([]);
  const dataId = ref(null);
  const editMode = ref(false);
  const nameError = ref(false);

  const nameErrorMessage = ref('');
  const bodyError = ref(false);

  const bodyErrorMessage = ref('');

  function handleFileChange(event, fileType) {
  const file = event.target.files[0];
  switch (fileType) {
    case "attachement":
    attachement.value = file;
      break;
 
  }
}
  
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
    isModalOpen.value = true;
  }
  
  function openModalDelete(dataInfo) {
    dataId.value = dataInfo.id;
    subject.value = dataInfo.subject;
    description.value = dataInfo.description;
    category.value = dataInfo.category;
    priority.value = dataInfo.priority;
    isModalOpenDelete.value = true;
  }
  
 
  function closeModal() {
    isModalOpen.value = false;
    isModalOpenDelete.value = false;
    subject.value = '';
    description.value=' ';
    category.value = '';
    priority.value=' ';
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
      const response = await axios.get('/ticket/get/list', config);
      dataList.value = response.data;
    } catch (error) {
      console.error('Error fetching todo data:', error);
    }
  };
  
  function validateName() {
    if (!subject.value || subject.value.length === 0) {
      nameError.value = true;
      nameErrorMessage.value = 'Subject Field cannot be empty';
      return false;
    } else {
      nameError.value = false;
      nameErrorMessage.value = '';
      return true;
    }
  }
  function validateBody() {
    if (!description.value || description.value.length === 0) {
      bodyError.value = true;
      bodyErrorMessage.value = 'Description Field cannot be empty';
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
    } else if (fieldName === 'subject') {
      validateName();
    } else if (fieldName === 'description') {
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
       
        const formData = new FormData();
        formData.append("subject", subject.value);
        formData.append("description", description.value);
        formData.append("category", category.value);
        formData.append("priority", priority.value);
        formData.append("dataId", dataId.value || null);
        if (attachement.value) formData.append("attachement", attachement.value);
        const response = await axios.post('/ticket/insert/update', formData, config);
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
      const response = await axios.post('/ticket/delete', payload, config);
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
  