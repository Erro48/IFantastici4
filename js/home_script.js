
/* -------------------------------------------------
  ONLOAD FUNCTIONS
----------------------------------------------------- */

window.onload = function(){
  getScoresDatasPromise().then(
    function(scores_data) {
      setCookie('scores_data', scores_data, 1);

      setGpsInSession(scores_data);

      getModScoresDatasPromise().then(
        function(mod_scores_data) {
          setCookie('mod_scores_data', mod_scores_data, 1);
          
          createTeamScoreFile(mod_scores_data);

          // prendo i piloti e la scuderia della squadra, e li metto nei cookie
          let drivers = getPersonalDriversFromCards();
          let arr = [];

          for(let i = 0; i < drivers.length; i++) {
            arr.push(drivers[i]);
          }

          arr.push(getPersonalStableFromCard());
          setCookie("team", arr, 1);

          readCSVFile(mod_scores_data);
          setCardsBackground();
          checkMegaDriver();
          
          createTrackSlot(getLastGpLocation());
          setTimeout(removeLoader, 2000);
          
        }
      )
    }
  );
  // attivo di tooltip di bootstrap
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
  });
}

function createTeamScoreFile(score) {

}

function setGpsInSession(score) {
  let gps = [];
  for(let i = 1; i < score.length; i++) {
    gps.push(score[i][0]);
  }

  $.ajax({
    type: "POST",
    url: '../Fanta/lib/request.php',
    data: { set_gps_session : gps }
  });
}

/* Promise per prendere i punteggi dei file score.csv e mod_score.csv */
function getScoresDatasPromise() {
  return new Promise(function(resolve, reject) {
    $.ajax({
      url: '../Fanta/data/score.csv',
      dataType: 'text'
    }).done(function(response) {
      let all_rows = response.split(/\r?\n|\r/);
      let data = [];
      for(let i = 0; i < all_rows.length; i++) {
        data.push(all_rows[i].split(','));
      }
      resolve(data);
    })
  });
}

function getModScoresDatasPromise() {
  return new Promise(function(resolve, reject) {
    $.ajax({
      url: '../Fanta/data/mod_score.csv',
      dataType: 'text'
    }).done(function(response) {
      let all_rows = response.split(/\r?\n|\r/);
      let data = [];
      for(let i = 0; i < all_rows.length; i++) {
        data.push(all_rows[i].split(','));
      }
      resolve(data);
    })
  });
}
  
function setCardsBackground(){
    let drivers_list = getPersonalDrivers();
    let stable = getPersonalStable();
  
    let drivers_index_list = getDriversIndexes(drivers_list);
    let stable_index = getStableIndex(stable);
  
    for(let i = 0; i < 5; i++) {
      setDriversCardBackground(getDriversCard(drivers_list[i]), drivers_index_list[i]);
      setLiveryColors(getDriversCard(drivers_list[i]), drivers_index_list[i]);
    }
  
    setStablesCardBackground(getStableCard(stable), stable_index);
}
  
function checkMegaDriver(){
  
    getMegaDriverInfoPromise().then((response) => {
      let mega_driver = JSON.parse(response).md_surname;
      let md_flag = JSON.parse(response).md_flag;
  
      if(md_flag == 0){
        // md mai stato usato
        return true;
      }
  
      // mettere controllo per metà stagione
  
      // md_flag == 1
      // prendo i punti dell'ultima gara di md
      // se punti base == putni effettivi o se non ci sono ancora punti --> md è per prossima gara
      // altrimenti era per gara scorsa, quindi toglierlo

      /*let data_score = scoreConverterToArray(getCookie("scores_data"));
      let driver_list = data_score[0];
      let driver_index = getDriverIndex(mega_driver);
      let last_gp_score = data_score[getLastGpIndex(data_score)][driver_index];*/
      
      let last_gp_index = getLastGpIndex(scoreConverterToArray(getCookie('scores_data'), 31));
      let base_score = getDriverScore(mega_driver, last_gp_index, "score");
      let mod_score = getDriverScore(mega_driver, last_gp_index, "mod_score");

      if(base_score == null || mod_score == null) {
        // md non settato
        return;
      } else if(base_score == 0 || mod_score == 0) {
        // tabella vuota
        return;
      } else if(base_score == mod_score) {
        // md è per il prossimo gp
        return;
      } else {
        // erano per lo scorso gp
        // devo settare a null il md
        $.ajax({
          type: "POST",
          url: '../Fanta/lib/request.php',
          data: { set_md_null : mega_driver }
        });
      }
  
      /*
      getDriverScorePromise(mega_driver, "score.csv").then(
        function(base_score) {
          getDriverScorePromise(mega_driver, "mod_score.csv").then(
            function(real_score) {
              if(base_score == 0 || real_score == 0) {
                // tabella vuota
                return;
              }else if(base_score == real_score) {
                // md è per prossimo gp
                return;
              } else {
                // erano per lo scorso gp
                // devo settare a null il md
                $.ajax({
                    type: "POST",
                    url: '../Fanta/lib/request.php',
                    data: { set_md_null : mega_driver }
                });
              }
          });
        });*/
    });
}


/* -------------------------------------------------
  GETTER
----------------------------------------------------- */

function getDriversCard(driver) {
    return document.getElementById(driver + "-card");
}

function getDriverFromTdMdDiv(elem) {
  console.log(elem.parentElement.parentElement.parentElement.parentElement.parentElement);
    return elem.parentElement.parentElement.parentElement.parentElement.parentElement.id.split("-")[0];
}

function getStableCard(stable) {
    return document.getElementById(stable + "-card");
}
  
function getObjectElements(object) {
  let arr = [];

  for(const property in object) {
      arr.push(`${object[property]}`);
  }

  arr.push(0);  // intervallo
  arr.push(0);  // leader

  return arr;
}


/* -------------------------------------------------
  SETTER
----------------------------------------------------- */

function setDriversCardBackground(card, index) {
    let header = card.getElementsByClassName('card-header')[0];
  
    header.style.backgroundColor = livery[parseInt((index-1)/2)];
    if(parseInt((index-1)/2) == 9){
      header.style.color = 'black';
    }
}

function setLiveryColors(card, index) {
  let body_img = card.getElementsByClassName('body-img')[0];
  
  body_img.style.borderBottom = "3px solid " + livery[parseInt((index-1)/2)];
  if(parseInt((index-1)/2) == 9){
    body_img.style.borderBottom = "3px solid black";
  }
}

function setStablesCardBackground(card, index) {
  let header = card.getElementsByClassName('card-header')[0];

  header.style.backgroundColor = livery[castScore(index-1)];
  if(castScore((index-1)/2) == 9){
      header.style.color = 'black';
  }
}

function setMegaDriver(elem) {
  let driver = getDriverFromTdMdDiv(elem);
  let turbo_driver = getDriverFromTdMdDiv(document.getElementsByClassName('turbo-driver')[0]);

  let mega_driver_flag;

  // prendo md (se settato) e md flag
  getMegaDriverInfoPromise().then(
      function(response) {
      old_mega_driver = JSON.parse(response).md_surname;
      mega_driver_flag = JSON.parse(response).md_flag;

  // controlla che non siamo in race weekend
      let next_gp = getCookie("next_gp");
      let next_gp_date = formatDate(next_gp.split('-')[1]);

      if(diffDate(next_gp_date, today()) <= 0) {
      alert("Le modifiche della squadra sono chiuse.");

      } else if(mega_driver_flag == 1){
      alert("Hai giù usato il MD.");

      } else if(!driver.localeCompare(turbo_driver)) {
      alert("Il MD non può essere un pilota che è anche TD.");

      } else {
      // md_flag == 0
      // setta MD
      let change_mega_driver = confirm("Stai cambiando il MD. Dopo averlo impostato non puoi più modificare la tua scelta. Vuoi procedere?");
      if(change_mega_driver){
          changeMegaDriver(driver);
      }
      }
  });

}

function setTurboDriver(elem){
  let driver = getDriverFromTdMdDiv(elem);
  let price = document.getElementById(driver + '-price').innerHTML.split(" ")[1].split(".")[0];

  // controlla che non siamo in race weekend
  let next_gp = getCookie("next_gp");
  let next_gp_date = formatDate(next_gp.split('-')[1]);

  if(diffDate(next_gp_date, today()) <= 0){
      alert("Le modifiche della squadra sono chiuse.");
  }else if(castScore(price) > 20){
      alert("Non puoi scegliere come turbo driver un pilota con un prezzo maggiore di 20.00$");
  }else{
      changeTurboDriver(driver);
  }
}


/* -------------------------------------------------
  PRINT
----------------------------------------------------- */

function printTeamTotalScore(team_id /*drivers_total_score, stable_total_score*/) {
    if(team_id == null)
      document.getElementById('total-points').innerHTML = "-";
    else {
      getTeamTotalScore(team_id).then(
        function(response) {
          document.getElementById('total-points').innerHTML = response/*(sumArray(drivers_total_score) + stable_total_score)*/;
        }
      )
    }
      
}

function printTeamLastGpScore(drivers_last_score, stable_last_score) {
  if(drivers_last_score == null)
      document.getElementById('raceweek-points').innerHTML = "-";
  else
      document.getElementById('raceweek-points').innerHTML = (sumArray(drivers_last_score) + castScore(stable_last_score));

      document.getElementById('raceweek-points').parentNode.childNodes[1].appendChild(getFlagElement(getLastGpLocation(), 'top', true));
 }

function printDriversLastGpScore(drivers_last_score) {
for(let i = 0; i < 5; i++) {
    if(drivers_last_score == null)
    document.getElementsByClassName('last-score')[i].innerHTML = getLastGpLocation() + ": -";
    else
    document.getElementsByClassName('last-score')[i].innerHTML = getLastGpLocation() + ": " + drivers_last_score[i];
}
}

function printStableLastGpScore(stable_last_score) {
if(stable_last_score == null)
    document.getElementById('stable-last-score').innerHTML = getLastGpLocation() + ": -";
else
    document.getElementById('stable-last-score').innerHTML = getLastGpLocation() + ": " + stable_last_score;
}

function printChart(object, type) {
let tbody = document.getElementById(type + "-chart").getElementsByClassName("tbody")[0];

let leader_score = object[0].score;
let prev_score = object[0].score;

// creo i figli
for(let i = 0; i < object.length; i++) {
    let chart_row = document.createElement("tr");
    let th_scope = document.createElement("th");
    let td_elem = getObjectElements(object[i]);

    chart_row.classList.add("charts-row-" + (i+1));
    chart_row.id = object[i][Object.getOwnPropertyNames(object[i])[0]];

    th_scope.scope = "row";
    th_scope.innerHTML = i+1;

    chart_row.appendChild(th_scope);

    for(let j = 0; j < td_elem.length; j++) {
    let td = document.createElement("td");

    if(j == (td_elem.length-2)) {
        // intervallo
        td.innerHTML = i == 0 ? "-" : prev_score - object[i].score;
        prev_score = object[i].score;

    } else if(j == (td_elem.length-1)) {
        // leader
        td.innerHTML = i == 0 ? "-" : leader_score - object[i].score;
    } else {
        td.innerHTML = td_elem[j];
    }

    if(isLongCell(Object.getOwnPropertyNames(object[i]), j)) {
        td.classList.add("cell-wrapper");
    }
    
    chart_row.appendChild(td);
    }

    // controlla se è un mio pilota
    if( (!type.localeCompare("driver") && isRowOfPersonalDriverOrStable(chart_row)) || 
        (!type.localeCompare("stable") && isRowOfPersonalDriverOrStable(chart_row)) ) {

    let children = chart_row.childNodes;
    let tmp_td = chart_row.childNodes[1];

    for(let i = 0; i< children.length; i++) {
        children[i].classList.add("my-driver-row");
    }

    tmp_td.setAttribute('data-bs-toggle', 'tooltip');
    tmp_td.setAttribute('data-bs-placement', 'top');
    tmp_td.title = !type.localeCompare("driver") ? "Tuo pilota" : "Tua scuderia";
    }

    tbody.appendChild(chart_row);
}

}


/* -------------------------------------------------
  OTHER FUNCTIONS
----------------------------------------------------- */

function readCSVFile(csv_data) {
    // prendo i miei 5 piloti + la scuderia
    let drivers_list = getPersonalDrivers();
    let stable = getPersonalStable();
    let team_id = getCookie('id_squadra');
  
    // prendo ultimo gp
    let last_gp_index = getLastGpIndex(csv_data);
    let gps = [];
    for(let i = 1; i < csv_data.length; i++) {
      gps.push(csv_data[i][0]);
    }



    setCookie("gps", gps, 1);

    if(last_gp_index == 0){
      // tabella vuota
      printTeamTotalScore(null);
      printTeamLastGpScore(null);
      printDriversLastGpScore(null);
      printStableLastGpScore(null);

      setCookie("next_gp", getGpByIndex(last_gp_index, csv_data), 1);
      setCookie("last_gp", null, 1);

      return;
    }
    

    // prendo i punteggi totali di ogni pilota e della scuderia
    let drivers_total_score = getDriversTotalScore(drivers_list, csv_data, last_gp_index);
    let stable_total_score = getStableTotalScore(stable, csv_data, last_gp_index);

    // prendo i punteggi dell'ultimo weekend di ogni pilota e della scuderia
    let drivers_last_score = getDriversLastScore(drivers_list, csv_data, last_gp_index);
    let stable_last_score = getStableLastScore(stable, csv_data, last_gp_index);

    setCookie("next_gp", getGpByIndex(last_gp_index + 1, csv_data), 1);
    setCookie("last_gp", getGpByIndex(last_gp_index, csv_data), 1);

    // stampo i punteggi (totali e parziali)
    //printTeamTotalScore(drivers_total_score, stable_total_score);
    printTeamTotalScore(team_id/*drivers_total_score, stable_total_score*/);
    printTeamLastGpScore(drivers_last_score, stable_last_score);
    printDriversLastGpScore(drivers_last_score);
    printStableLastGpScore(stable_last_score);

    return csv_data;
}

function changeTeamName(){
  let old_name = document.getElementById('team-name').innerHTML;
  let new_name = prompt("Metti il nome della squadra", old_name);

  if(new_name == null || old_name.localeCompare(new_name) == 0){
    return;
  }else{
    $.ajax({
      type: "POST",
      url: '../Fanta/lib/request.php',
      data: { new_team_name : new_name },
      success: function(response)
      {
        document.getElementById('team-name').innerHTML = new_name;

        let chart_row = document.getElementById(old_name);
        chart_row.childNodes[1].innerHTML = new_name;

        chart_row.id = new_name;
      }
    });
  }
}

function changeMegaDriver(driver) {
  $.ajax({
    type: "POST",
    url: '../Fanta/lib/request.php',
    data: { new_md : driver },
    success: function(response)
    {

      let new_md_elem = document.getElementById(driver + '-card');
      let md_btn = new_md_elem.getElementsByClassName('md-btn')[0];

      md_btn.classList.add('mega-driver');
    }
  });
}

function changeTurboDriver(driver) {
  $.ajax({
    type: "POST",
    url: '../Fanta/lib/request.php',
    data: { new_td : driver },
    success: function(response)
    {

      let new_td_elem = document.getElementById(driver + '-card');
      let td_btn = new_td_elem.getElementsByClassName('td-btn')[0];

      let old_td_btn = document.getElementsByClassName('turbo-driver')[0];

      old_td_btn.classList.remove('turbo-driver');
      td_btn.classList.add('turbo-driver');
    }
  });
}

function getTeamTotalScore(team_id) {
  return new Promise(function(resolve){
    $.ajax({
      type: "POST",
      url: '../Fanta/lib/request.php',
      data: { get_team_score : team_id },
      datatype: 'json',
      success: function(response) {
        resolve(response);
      },
      error: function(err) {
        reject(err)
      }
    });
  });
}

function recursiveGetDrive(json_elem, drivers_list, scores_list, all_rows, last_gp_index, i) {
  if(i == json_elem.length) {
    // ha preso tutte le squadre
    let team_obj = [];
    
    for(let j = 0; j < drivers_list.length; j++) {
      /*let driver_total = getDriversTotalScore(drivers_list[j].slice(0, 5), all_rows, last_gp_index);
      let stable_total = getStableTotalScore(drivers_list[j][5], all_rows, last_gp_index);*/

      let d_indexes = getDriversIndexes(drivers_list[j]);
      let driver_partial = getDriversPartialPerEachGp(d_indexes, all_rows, last_gp_index);
      let stable_partial = getStablePartialPerEachGp(getStableIndex(drivers_list[j][5]), all_rows, last_gp_index);
      
      driver_partial = formatDriversPartial(driver_partial);
      driver_partial = extractLastGpScore(driver_partial);

      let team_partial = sumArray(driver_partial) + stable_partial[last_gp_index-1];


      team_obj.push(
        {
          team_name: json_elem[j].nome_squadra,
          team_owner: json_elem[j].nome_utente,
          score: scores_list[j],
          partial: team_partial
        });

    }

    team_obj = sortTeamsByScore(team_obj);
    setCookie('ordered_teams_chart', team_obj.map(function(e) { return e.team_name}), 1);
    printChart(team_obj, "team");


    

  }else{
    getDriversAndStableFromTeamIdPromise(json_elem[i].id_squadra).then(
      function(drivers_team) {
        drivers_list.push(drivers_team);

        getTeamTotalScore(json_elem[i].id_squadra).then(
          function(scores_team) {
            scores_list.push(scores_team);
            recursiveGetDrive(json_elem, drivers_list, scores_list, all_rows, last_gp_index, i+1);
          }
        )

        
    });
  }
  
}

function extractLastGpScore(driver_partial) {
  let new_arr = [];
    for(let i = 0; i < driver_partial.length; i++) {
      new_arr.push(driver_partial[i][driver_partial[i].length-1]);
    }

    return new_arr;
}

function formatDriversPartial(drivers_partial) {
  let pure_partial = [];

  drivers_partial = sortById(drivers_partial);
  
  for(let i = 0; i < drivers_partial.length - 1; i++) {
    pure_partial.push(drivers_partial[i].score);
  }

  return pure_partial;
}

function createTeamChart(json_elem) {
  let mod_score = scoreConverterToArray(getCookie('mod_scores_data'), 31);
  let last_gp_index = getLastGpIndex(mod_score);

  let drivers_list = [];
  let scores_list = [];
  recursiveGetDrive(json_elem, drivers_list, scores_list, mod_score, last_gp_index, 0);
}

function createDriversChart(json_elem) {
  let score = scoreConverterToArray(getCookie('scores_data'), 31);
  let last_gp_index = getLastGpIndex(score);

  let drivers_list = [];

  for(let i = 1; i <= 20; i++) {
    drivers_list.push(score[0][i]);
  }

  let driver_total = getDriversTotalScore(drivers_list, score, last_gp_index);
  let drivers_obj = [];

  for(let i = 0; i < json_elem.length; i++) {
    drivers_obj.push({
      driver_surname: json_elem[i].cognome_pilota,
      driver_name: json_elem[i].nome_pilota,
      score: driver_total[i],
      driver_stable: json_elem[i].nome_scuderia
    });
  }

  drivers_obj = sortTeamsByScore(drivers_obj);

  let tmp = [];
  for(let i = 0; i < drivers_obj.length; i++) {
    tmp.push(drivers_obj[i].driver_surname);
  }

  setCookie("ordered_drivers_chart", tmp, 1);


  printChart(drivers_obj, "driver");
}

function createStableChart(json_elem) {
  let score = scoreConverterToArray(getCookie('scores_data'), 31);
  let last_gp_index = getLastGpIndex(score);

  let stables_list = [];

  for(let i = 21; i <= 30; i++) {
    stables_list.push(score[0][i]);
  }

  let stables_total = getStablesTotalScore(stables_list, score, last_gp_index);
  let stables_obj = [];

  for(let i = 0; i < json_elem.length; i++) {
    stables_obj.push({
      stable_name: json_elem[i].nome_scuderia,
      stable_short_name: json_elem[i].nome_breve,
      score: stables_total[i]
    });
  }

  stables_obj = sortTeamsByScore(stables_obj);

  let tmp = [];
  for(let i = 0; i < stables_obj.length; i++) {
    tmp.push(stables_obj[i].stable_name);
  }

  setCookie("ordered_stables_chart", tmp, 1);

  printChart(stables_obj, "stable");
}

function sortTeamsByScore(object) {
  for(let i = 1; i < object.length; i++) {
    let key = object[i];
    let j = i - 1;

    while(j >= 0 && object[j].score < key.score) {
      object[j + 1] = object[j];
      j--;
    }

    object[j + 1] = key;
  }

  return object;
}

function sortById(object) {
  for(let i = 1; i < object.length; i++) {
    let key = object[i];
    let j = i - 1;

    while(j >= 0 && object[j].driver > key.driver) {
      object[j + 1] = object[j];
      j--;
    }

    object[j + 1] = key;
  }

  return object;
}

function isLongCell(object, current_index) {
    if(object[current_index] == "team_name") {
      // squadra
      return true;
    }
  
    if(object[current_index] == "driver_stable") {
      // pilota
      return true;
    }
  
    if(object[current_index] == "stable_name" || object[current_index] == "stable_short_name") {
      return true;
    }
}
  
function isRowOfPersonalDriverOrStable(row) {
    if(getPersonalDrivers().includes(row.childNodes[1].innerHTML)) {
      return true;
    }
  
    if(!getPersonalStable().localeCompare(row.childNodes[2].innerHTML)) {
      return true;
    }
  
    return false;
}

/* |-- TRACK SLOT --------------------------------- */

function createTrackSlot(gp_location) {
  let score = scoreConverterToArray(getCookie('scores_data'), 31);
  let last_gp_date = getGpDate(gp_location);
  let last_gp_index = getGpIndex(gp_location, score);

  let track_slot = document.getElementById('track-slot');
  let header = createTrackHeader(score, gp_location);
  let body = createTrackBody(score, gp_location);
  let footer = createTrackFooter();
  

  track_slot.appendChild(header);
  track_slot.appendChild(body);
  track_slot.appendChild(footer);
}

function createTrackHeader(score, gp) {
  let header = document.createElement('ul');
  header.classList.add('track-slot-header');
  header.classList.add('nav');
  header.classList.add('nav-tabs');
  header.setAttribute('id', 'track-slot-header');

  let prevs_gp = createHeaderArrows(0, gp, score); //document.createElement('li');
  let header_gps = [];
  let nexts_gp = createHeaderArrows(1, gp, score); //document.createElement('li');

  /*prevs_gp.classList.add('header-elem');
  prevs_gp.classList.add('nav-item');
  prevs_gp.classList.add('nav-link');
  prevs_gp.id = 'prev-arrow';
  prevs_gp.innerHTML = '<<';
  prevs_gp.setAttribute('onclick', 'createAllGpView(0, \'' + gp + '\')');*/

  /*nexts_gp.classList.add('header-elem');
  nexts_gp.classList.add('nav-item');
  nexts_gp.classList.add('nav-link');
  nexts_gp.id = 'next-arrow';
  nexts_gp.innerHTML = '>>';
  nexts_gp.setAttribute('onclick', 'createAllGpView(1, \'' + gp + '\')'); */

  for(let i = 0; i < 3; i++) {
    let gp_location;
    header_gps.push(document.createElement('li'));
    header_gps[i].classList.add('header-elem');
    header_gps[i].classList.add('nav-item');
    header_gps[i].classList.add('nav-link');
    
    let gp_index = getGpIndex(gp, score);
    if(i == 0) {
      gp_location = getGpByIndex(gp_index-1, score).split('-')[0];
    } else if(i == 1) {
      gp_location = gp;
    } else {
      gp_location = getGpByIndex(gp_index+1, score).split('-')[0];
    }

    let gp_flag = getFlagElement(gp_location, 'top', false);
    let gp_txt = document.createElement('div');
    gp_txt.classList.add('gp-location');
    gp_txt.classList.add('d-none');
    gp_txt.classList.add('d-sm-flex');
    gp_txt.innerHTML = gp_location.substring(0, 3);

    if(i != 1) {
      if(gp_location != '')
        header_gps[i].setAttribute('onclick', 'loadTrackLayoutSlot(\'' + gp_location + '\')'); 
    }

    if(gp_location != '')
      header_gps[i].appendChild(gp_flag);
    header_gps[i].appendChild(gp_txt);
  }
  
  header_gps[1].classList.add('active');
  header_gps[1].classList.add('last-gp');


  header.appendChild(prevs_gp);
  for(let i = 0; i < 3; i++ ) { header.appendChild(header_gps[i]); }
  header.appendChild(nexts_gp);

  

  /*header.appendChild(accordion_btn_container);*/
  return header;
}

function createTrackBody(score, gp) {
  let gp_index = getGpIndex(gp, score);

  let body = document.createElement('div');
  body.classList.add('track-slot-body');
  /*body.classList.add('accordion-collapse');
  body.classList.add('collapse');*/
  body.classList.add('row');
  /*body.setAttribute('id', 'track-slot-body');
  body.setAttribute('aria-labelledby', 'track-slot-header');
  body.setAttribute('data-bs-parent', '#accordionExample');*/

  let gp_location = document.createElement('div');
  let gp_date = document.createElement('div');
  let track_layout = document.createElement('div');
  let track_rank = document.createElement('div');

  gp_location.innerHTML = getGpByIndex(gp_index, score).split('-')[0];
  gp_location.classList.add('gp-location');
  
  gp_date.innerHTML = getGpByIndex(gp_index, score).split('-')[1];
  gp_date.classList.add('gp-date');

  track_layout.classList.add('track-layout');
  //track_layout.appendChild(getTrackLayout(getGpByIndex(gp_index, score).split('-')[0]));
  track_layout.appendChild(getTrackLayoutImage(getGpByIndex(gp_index, score).split('-')[0]));

  track_rank = getTrackRank(gp_index);
  console.log(track_rank)

  body.appendChild(gp_location);
  body.appendChild(gp_date);
  body.appendChild(track_layout);
  body.appendChild(track_rank);

  return body;
}

function createTrackFooter() {
  let footer = document.createElement('div');
  return footer;
}

function getTrackRank(gp_index) {
  let score = scoreConverterToArray(getCookie('scores_data'), 31);

  let rank_container = document.createElement('div');
  rank_container.classList.add('track-rank');

  let drivers_rank = getDriversTrackSlotRank(score, gp_index);
  let stables_rank = getStablesBestAndWorst(score, gp_index);
  let teams_rank = getTeamsBestAndWorst(score, gp_index);

  rank_container.appendChild(drivers_rank);
  rank_container.appendChild(stables_rank);
  rank_container.appendChild(teams_rank);
  return rank_container;
}

function getDriversTrackSlotRank(score, gp_index) {
  let drivers_rank = document.createElement('div');
  let drivers_rank_title = document.createElement('div');

  let drivers_total = [];
  let ordered_indexes;
  
  drivers_rank.classList.add('row');
  drivers_rank_title.classList.add('col-12');
  drivers_rank_title.classList.add('rank-title');
  drivers_rank_title.innerHTML = 'Piloti';

  drivers_rank.appendChild(drivers_rank_title);

  for(let i = 1; i <= 20; i++) {
    drivers_total.push(getDriverPartialPerEachGp(i, score, gp_index)[gp_index - 1]);
  }
  
  ordered_indexes = calculateRank(drivers_total, 3);

  for(let i = 0; i < ordered_indexes.length; i++) {
    let div = document.createElement('div');
    let driver;
    
    if(gp_index <= getLastGpIndex(score)) {
      driver = createRankElement(score[0][ordered_indexes[i]].substring(0, 3), score[gp_index][ordered_indexes[i]], true);
      div.style.borderLeft = '3px solid ' + getLiveryByDriverId(ordered_indexes[i]);
    } else {
      driver = createRankElement('-', 0, true);
    }
    
    div.classList.add('col');
    div.classList.add('track-rank-elem');

    div.appendChild(driver);
    drivers_rank.appendChild(div);
  }

  return drivers_rank;
}

function getStablesBestAndWorst(score, gp_index) {
  let stables_rank = document.createElement('div');
  let best_stable_div = document.createElement('div');
  let worst_stable_div = document.createElement('div');

  let stable_title = document.createElement('div');
  let stable_subtitle = document.createElement('div');
  let best_txt = document.createElement('div');
  let worst_txt = document.createElement('div');
  
  
  stables_rank.classList.add('row');
  best_stable_div.classList.add('col');
  best_stable_div.classList.add('track-rank-elem');
  worst_stable_div.classList.add('col');
  worst_stable_div.classList.add('track-rank-elem');
  
  stable_title.classList.add('col-12');
  stable_title.classList.add('rank-title');
  stable_title.innerHTML = 'Scuderie';
  stable_subtitle.classList.add('row');
  stable_subtitle.classList.add('rank-subtitle');

  best_txt.classList.add('col');
  best_txt.innerHTML = 'Migliore';

  worst_txt.classList.add('col');
  worst_txt.innerHTML = 'Peggiore';

  stable_subtitle.appendChild(best_txt);
  stable_subtitle.appendChild(worst_txt);

  stables_rank.appendChild(stable_title);
  stables_rank.appendChild(stable_subtitle);
  
  let stable_total = [];
  for(let i = 1; i <= 10; i++) {
    stable_total.push(getStablePartialPerEachGp(i, score, gp_index)[gp_index - 1]);
  }
  
  let best_stable = calculateRank(stable_total, 1)[0];
  let worst_stable = calculateRank(stable_total, -1)[0];
  
  
  if(gp_index <= getLastGpIndex(score)) {
    best_stable_div.appendChild(createRankElement(score[0][20 + best_stable], score[gp_index][20 + best_stable], false));
    worst_stable_div.appendChild(createRankElement(score[0][20 + worst_stable], score[gp_index][20 + worst_stable], false));
    best_stable_div.style.borderLeft = '3px solid ' + getLiveryByStableId(best_stable);
    worst_stable_div.style.borderLeft = '3px solid ' + getLiveryByStableId(worst_stable);
  } else {
    best_stable_div.appendChild(createRankElement('-', 0, false));
    worst_stable_div.appendChild(createRankElement('-', 0, false));
  }

  stables_rank.appendChild(best_stable_div);
  stables_rank.appendChild(worst_stable_div);

  return stables_rank;
}

function getTeamsBestAndWorst(score, gp_index) {
  let teams_rank = document.createElement('div');
  let best_team_div = document.createElement('div');
  let worst_team_div = document.createElement('div');
  
  let team_title = document.createElement('div');
  let team_subtitle = document.createElement('div');
  let team_best_txt = document.createElement('div');
  let team_worst_txt = document.createElement('div');

  teams_rank.classList.add('row');

  best_team_div.classList.add('col');
  best_team_div.classList.add('track-rank-elem');
  worst_team_div.classList.add('col');
  worst_team_div.classList.add('track-rank-elem');

  team_title.classList.add('col-12');
  team_title.classList.add('rank-title');
  team_title.innerHTML = 'Squadre';

  team_subtitle.classList.add('row');
  team_subtitle.classList.add('rank-subtitle');
  team_best_txt.classList.add('col');
  team_best_txt.innerHTML = 'Migliore';

  team_worst_txt.classList.add('col');
  team_worst_txt.innerHTML = 'Peggiore';
  team_subtitle.appendChild(team_best_txt);
  team_subtitle.appendChild(team_worst_txt);

  teams_rank.appendChild(team_title);
  teams_rank.appendChild(team_subtitle);

  getFileContentPromise('teams_score.csv').then(
    function(teams_score) {
      teams_score = teams_score.split('\n').map(function(e) { return e.split(',') });
      let last_score = teams_score[gp_index].slice(1).map(function(e) { return castScore(e)});
      let best_team = calculateRank(last_score, 1);
      let worst_team = calculateRank(last_score, -1);

      
      getTeamsInfoPromise().then(
        function(teams_obj) {
          teams_obj = JSON.parse(teams_obj).map(function(e) { return JSON.parse(e)});
          
          if(gp_index <= getLastGpIndex(score)) {
            best_team_div.appendChild(createRankElement(
              teams_obj[teams_score[0][best_team] - 1].nome_squadra,
              teams_score[gp_index][best_team],
              false
              ));
  
            worst_team_div.appendChild(createRankElement(
              teams_obj[teams_score[0][worst_team] - 1].nome_squadra,
              teams_score[gp_index][worst_team],
              false
              ));
          } else {
            best_team_div.appendChild(createRankElement('-', 0, false));
  
            worst_team_div.appendChild(createRankElement('-', 0, false));
          }
          
          
          
          teams_rank.appendChild(best_team_div);
          teams_rank.appendChild(worst_team_div);
          //teams_rank.appendChild(team_div);
        }
      )
    }
  );

  return teams_rank;
}

function calculateRank(arr, rank_dim) {
  let ordered_indexes = [];
  let tmp_arr = arr.slice();

  if(rank_dim > 0) {
    for(let i = 0; i < Math.abs(rank_dim); i++) {
      let max = 0, max_index = -1;
      for(let j = 0; j < tmp_arr.length; j++) {
        if(tmp_arr[j] >= max) {
          max = tmp_arr[j];
          max_index = j; 
        }
      }

      ordered_indexes.push(max_index + 1);
      tmp_arr[max_index] = 0;
    }
  } else {
    for(let i = 0; i < Math.abs(rank_dim); i++) {
      let min = tmp_arr[0], min_index = -1;
      for(let j = 0; j < tmp_arr.length; j++) {
        if(tmp_arr[j] <= min) {
          min = tmp_arr[j];
          min_index = j; 
        }
      }

      ordered_indexes.push(min_index + 1);
      tmp_arr[min_index] = 0;
    }
  }
  

  return ordered_indexes;
}

function createRankElement(value, score, driver_flag) {
  let div = document.createElement('div');
  div.classList.add('row');

  let val_txt = document.createElement('div');
  let score_txt = document.createElement('div');

  val_txt.classList.add('rank-name');
  score_txt.classList.add('rank-score');

  if(driver_flag == false) {
    /*val_txt.classList.add('col-12');
    score_txt.classList.add('col-12');
    val_txt.classList.add('col-sm-6');
    score_txt.classList.add('col-sm-6');*/
    val_txt.classList.add('col-12');
    score_txt.classList.add('col-12');
  } else {
    val_txt.classList.add('col');
    score_txt.classList.add('col');
  }

  val_txt.innerHTML = value;
  score_txt.innerHTML = ' (' + score + ')';

  div.appendChild(val_txt);
  div.appendChild(score_txt);

  return div;
}

function loadTrackLayoutSlot(gp_location) {
  let track_slot = document.getElementById('track-slot');

  while (track_slot.firstChild) {
    track_slot.removeChild(track_slot.firstChild);
  }

  createTrackSlot(gp_location);

}

function createHeaderArrows(side, gp, score) {
  let dropdown_container = document.createElement('li');
  let dropdown_btn = document.createElement('button');
  let dropdown_ul = document.createElement('ul');

  dropdown_container.classList.add('btn-group');
  side == 0 ? dropdown_container.classList.add('dropend') : dropdown_container.classList.add('dropstart');

  dropdown_btn.classList.add('btn');
  dropdown_btn.classList.add('dropdown-toggle');
  dropdown_btn.classList.add('dropdown-arrows');
  dropdown_btn.setAttribute('type', 'button');
  dropdown_btn.setAttribute('data-bs-toggle', 'dropdown');
  dropdown_btn.setAttribute('aria-expanded', 'false');
  dropdown_btn.innerHTML = side == 0 ? '<<' : '>>';

  dropdown_ul.classList.add('dropdown-menu');


  let all_gps = getCookie('gps').split(',').map(function(e) { return e.split('-')[0]; });
  let gp_index = getGpIndex(gp, score);
  let selected_gps = side == 0 ? all_gps.slice(0, gp_index - 2) : all_gps.slice(gp_index + 1) ;

  console.log(selected_gps);
  for(let i = 0; i < selected_gps.length; i++) {
    let dropdown_li = document.createElement('li');
    dropdown_li.setAttribute('onclick', 'loadTrackLayoutSlot(\'' + selected_gps[i] + '\')');
    dropdown_li.innerHTML = selected_gps[i];

    if(selected_gps[i] == getLastGpLocation()) {
      dropdown_li.classList.add('last-gp');
    }

    dropdown_ul.appendChild(dropdown_li);
  }


  dropdown_container.appendChild(dropdown_btn);
  dropdown_container.appendChild(dropdown_ul);

  return dropdown_container;
}