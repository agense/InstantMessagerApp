window.addEventListener('load',function(){
    const sidebarToggler = document.querySelector('#sd-menu-toggler');
    const sidebar = document.querySelector('#sidebar-menu');
    const chatCol = document.querySelector('#chat-column');

    sidebarToggler.addEventListener('click', (e) => {
        if(!sidebar.classList.contains('sidebar-closed')){
            sidebar.classList.add('sidebar-closed');
            chatCol.classList.add('chat-coll-full');
        }else{
            sidebar.classList.remove('sidebar-closed');
            chatCol.classList.remove('chat-coll-full');
        }
    });
});
