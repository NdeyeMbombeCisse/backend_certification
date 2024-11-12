<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Réservation</title>
<!-- <style>

.section-contente{
  display: flex;
}
.ticket{
    width: 1436px;
    height: 530px;
    margin-top: 30px;
    border: 4px solid #227093;
    background: var(--couleur-primaire, #FFF);
    justify-content: center; 
    align-items: center;
}



.style{
    width: 100%;
    height: 100px;
    background: #227093;
}
.logo{
    display: flex;
    flex-direction: row;
}
.logo img{
    width: 90px;
    height: auto;
}

.titre{
 background: #227093;
 color: white;
overflow: hidden;
text-overflow: ellipsis;
font-family: Inter;
font-size: 14px;
font-style: normal;
font-weight: 700;
line-height: normal;
letter-spacing: 7.92px;
text-transform: uppercase;
width: 70%;
height: 73px;
border-radius: 20px;
margin: 20px;
}


.info-perso{
    width: 60%;
    height: 100vh;
}


.titre-section{
    display: flex;
    flex-direction: row;
    background: white;
    border: 4px solid #227093;
    border-radius: 20px;
    gap: 70px;
    
    height: 94px;
    justify-content: center;
    align-items: center;
    text-align: center;
    margin-top: -50px;
    margin-left:50px ;
}


.left-section{
  display: flex;
  margin-left: 20px;
}

.info p{
  white-space: nowrap;
  font-family: Inter;
  font-size: 14px;
  font-style: normal;
  line-height: normal;
  letter-spacing: 2px;
  text-transform: uppercase;

}
.info h3{
overflow: hidden;
text-overflow: ellipsis;
font-family: Inter;
font-size: 16px;
font-style: normal;
font-weight: 700;
line-height: 1;
letter-spacing: 4px;
text-transform: uppercase;

}

.info_passager{
  margin-top: 20px;
}

.qr-code img{
  width: 230px;
  height: 230px;
  margin-left: 60px;
  margin-top: 30px;
}

.info_passager2{
  margin-top: 20px;
  margin-left: 20px;

}


.titreCategorie{
  flex-direction: row;
  background: white;
  border: 4px solid #227093;
  border-radius: 20px;
  gap: 70px;
  width: 410px;
  height: 94px;
  justify-content: center;
  align-items: center;
  text-align: center;
  margin-top: -50px;
  margin-left:110px ;
  display: flex;
  align-items: center; 
  justify-content: center;

}

.titreCategorie h2 {
  white-space: nowrap;
  font-family: Inter;
  font-size: 18px;
  font-style: normal;
  font-weight: 700;
  line-height: normal;
  letter-spacing: 2px;
  text-transform: uppercase;
  text-align: center; 
}


.right-section{
  margin-top: 30px;

  margin-left: 190px;
  display: flex;
}

.dashed-line {
  border-left: 1px dashed #2c2a2a; 
  height: 300px;  
  margin-left: -70px; 
  margin-right: 30px;
  margin-top: -27px;
}


.bottom_section{
  background: #227093;
  width: 455px;
  height: 75px;
  margin-left: -30px;
  display: flex;
}

.destination h3{
  color: #FFF;
  text-transform: uppercase;
  font-weight: 700;
  margin-left: 30px;
  letter-spacing: 5px;
  margin-top: -7px;



}

.destination p{
  color: #FFF;
  text-transform: uppercase;
  font-weight: 700;
  margin-left: 50px;
  letter-spacing: 5px;


}

.destination2 h3{
  color: #FFF;
  text-transform: uppercase;
  font-weight: 700;
  margin-left: 130px;
  letter-spacing: 5px;
  margin-top: -7px;


}

.destination2 p{
  color: #FFF;
  text-transform: uppercase;
  font-weight: 700;
  margin-left: 160px;
  letter-spacing: 5px;


}

</style> -->
</head>
<body>



<div class="ticket">
<div class="style"></div>

<div class="section-contente">

<div class="info-perso">
    <div class="titre-section">
        <div class="logo">
            <img src="./../../../../assets/images/logo.png" alt="">
            <span>COSAMA RESERVATION</span>
        </div>
        <div class="titre">
            <h2>Ticket de Réservation</h2>
        </div>
    </div>

    <div class="left-section">
        <div class="info_passager">
            <div class="info">
                <p>Nom du passager :</p>
                <h3>{{ $ticketContent['user'] }}</h3>
            </div>

            <div class="info">
                <p>Téléphone :</p>
                <h3>{{ $ticketContent['telephone'] }}</h3>
            </div>

            <div class="info">
                <p>Email :</p>
                <h3>{{ $ticketContent['email'] }}</h3>
            </div>

            <div class="info">
                <p>Nationalité :</p>
                <h3>{{ $ticketContent['nationalite'] }}</h3>
            </div>

            <div class="info">
                <p>Date de Réservation :</p>
                <h3>{{ \Carbon\Carbon::parse($ticketContent['date_reservation'])->format('d/m/Y H:i') }}</h3>
            </div>
        </div>

        <div class="info_passager2">
            <div class="info">
                <p>Date de Départ :</p>
                <h3>{{ \Carbon\Carbon::parse($ticketContent['date_depart'])->format('d/m/Y') }}</h3>
            </div>

            <div class="info">
                <p>Heure d'Embarquement :</p>
                <h3>{{ $ticketContent['embarquement'] }}</h3>
            </div>

            <div class="info">
                <p>Heure de Départ :</p>
                <h3>{{ $ticketContent['depart'] }}</h3>
            </div>

            <div class="info">
                <p>Numéro de Réservation :</p>
                <h3>{{ $ticketContent['reservation_id'] }}</h3>
            </div>
        </div>

        <div class="qr-code">
            <img src="{{ $ticketContent['qr_code'] }}" alt="QR Code de réservation" />
        </div>
    </div>

</div>

<div class="categorie_section">
    <div class="titreCategorie">
        <h2>Catégorie de Place</h2>
    </div>

    <div class="right-section">
        <div class="dashed-line"></div>
        <div class="autres">
            <div class="info">
                <p>Catégorie :</p>
                <h3>{{ $ticketContent['categorie'] }}</h3>
            </div>

            <div class="info">
                <p>Tarif :</p>
                <h3>{{ $ticketContent['tarif'] }}</h3>
            </div>

            <div class="info">
                <p>Place :</p>
                <h3>{{ $ticketContent['place'] }}</h3>
            </div>

            <div class="info">
                <p>Statut :</p>
                <h3>{{ $ticketContent['statut'] ? 'Confirmée' : 'Non Confirmée' }}</h3>
            </div>

            <div class="bottom_section">
                <div class="destination">
                    <p>De</p>
                    <h3>{{ $ticketContent['trajet'] }}</h3>
                </div>

                <div class="destination2">
                    <p>Vers</p>
                    <h3>{{ $ticketContent['trajet'] }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <p>Merci d'avoir réservé avec nous !</p>
</div>
</div>

</div>


</body>
</html>
