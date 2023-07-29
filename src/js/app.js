document.addEventListener('DOMContentLoaded', function(){
    handleClickCard();
    handleClickMenu(); 
});


const handleClickCard = ()=>{
    //se declaran las variables y su seleccion en el dom
    const cards = document.getElementsByClassName("card--burger");
    const btnClose = document.querySelectorAll(".btnClose");
    //se añaden las funciones y eventos
    for( let i=0; i< cards.length; i++){
        cards[i].addEventListener('click', e => {
            e.preventDefault();

            const modal = document.querySelectorAll(".modal--burger")[i];
           
            openModal(modal);
        });
    }

    for( let i=0; i< btnClose.length; i++){
    
        btnClose[i].addEventListener('click', e => {
            e.preventDefault();

            const modal = document.querySelectorAll(".modal--burger")[i];
            closeModal(modal);
        });
    }


}

const handleClickMenu = ()=>{
    //se declaran las variables y su seleccion en el dom
    const btn = document.getElementById("btn-menu")
    const nav = document.getElementById("nav")

    btn.addEventListener('click', e => {
        e.preventDefault();
        nav.classList.toggle("active")
        btn.classList.toggle("active")
    });
    
}

const openModal = (card)=>{
    if(!card.classList.contains("active")){
        card.classList.add("active")
    }
}

const closeModal = (card)=>{
    if(card.classList.contains("active")){
        card.classList.remove("active")
    }
}