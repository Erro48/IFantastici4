
/* -------------------------------------------------
  ONLOAD FUNCTIONS
----------------------------------------------------- */

window.onload = function(){
    readTeamScore();
    setCardsBackground();
    checkMegaDriver();

    let drivers = getPersonalDrivers();
    let arr = [];

    for(let i = 0; i < drivers.length; i++) {
      arr.push(drivers[i]);
    }

    arr.push(getPersonalStable());
    setCookie("team", arr, 1);
  
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
    });
}
  
function readTeamScore() {
    $.ajax({
          url: '../Fanta/data/mod_score.csv',
          dataType: 'text',
        }).done(readCSVFile);
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
        });
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

header.style.backgroundColor = livery[parseInt(index-1)];
if(parseInt((index-1)/2) == 9){
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
}else if(parseInt(price) > 20){
    alert("Non puoi scegliere come turbo driver un pilota con un prezzo maggiore di 20.00$");
}else{
    changeTurboDriver(driver);
}
}


/* -------------------------------------------------
  PRINT
----------------------------------------------------- */

function printTeamTotalScore(drivers_total_score, stable_total_score) {
    if(drivers_total_score == null)
      document.getElementById('total-points').innerHTML = "-";
    else
      document.getElementById('total-points').innerHTML = (sumArray(drivers_total_score) + stable_total_score);
}

function printTeamLastGpScore(drivers_last_score, stable_last_score) {
  if(drivers_last_score == null)
      document.getElementById('raceweek-points').innerHTML = "-";
  else
      document.getElementById('raceweek-points').innerHTML = (sumArray(drivers_last_score) + parseInt(stable_last_score));

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

function readCSVFile(response) {
    // prendo i miei 5 piloti + la scuderia
    let drivers_list = getPersonalDrivers();
    let stable = getPersonalStable();
  
    // prendo ultimo gp
    getLastGpIndexPromise().then(
      function(last_gp_index) {
        let all_rows = response.split(/\r?\n|\r/);

        let gps = [];
        for(let i = 1; i < all_rows.length; i++) {
          gps.push(all_rows[i].split(",")[0]);
        }

        setCookie("gps", gps, 1);
  
        if(last_gp_index == 0){
          // tabella vuota
          printTeamTotalScore(null);
          printTeamLastGpScore(null);
          printDriversLastGpScore(null);
          printStableLastGpScore(null);
  
          setCookie("next_gp", getGpByIndex(last_gp_index, all_rows), 1);
          setCookie("last_gp", null, 1);
  
          return;
        }
  
        // prendo i punteggi totali di ogni pilota e della scuderia
        let drivers_total_score = getDriversTotalScore(drivers_list, all_rows, last_gp_index);
        let stable_total_score = getStableTotalScore(stable, all_rows, last_gp_index);
  
        // prendo i punteggi dell'ultimo weekend di ogni pilota e della scuderia
        let drivers_last_score = getDriversLastScore(drivers_list, all_rows, last_gp_index);
        let stable_last_score = getStableLastScore(stable, all_rows, last_gp_index);

        setCookie("next_gp", getGpByIndex(last_gp_index + 1, all_rows), 1);
        setCookie("last_gp", getGpByIndex(last_gp_index, all_rows), 1);
  
        // stampo i punteggi (totali e parziali)
        printTeamTotalScore(drivers_total_score, stable_total_score);
        printTeamLastGpScore(drivers_last_score, stable_last_score);
        printDriversLastGpScore(drivers_last_score);
        printStableLastGpScore(stable_last_score);
  
      },
      function(error) {
        console.log(error.message);
      }
    );
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

function recursiveGetDrive(json_elem, drivers_list, all_rows, last_gp_index, i) {
  if(i == json_elem.length) {
    // ha preso tutte le squadre
    let team_obj = [];
    
    for(let j = 0; j < drivers_list.length; j++) {
      let driver_total = getDriversTotalScore(drivers_list[j].slice(0, 5), all_rows, last_gp_index);
      let stable_total = getStableTotalScore(drivers_list[j][5], all_rows, last_gp_index);

      let d_indexes = getDriversIndexes(drivers_list[j]);
      let driver_partial = getDriversPartialPerEachGp(d_indexes, all_rows, last_gp_index);
      let stable_partial = getStablePartialPerEachGp(getStableIndex(drivers_list[j][5]), all_rows, last_gp_index);
      
      driver_partial = formatDriversPartial(driver_partial);
      driver_partial = extractLastGpScore(driver_partial);

      let team_partial = sumArray(driver_partial) + stable_partial[last_gp_index-1];

      let team_total = sumArray(driver_total) + stable_total;

      team_obj.push(
        {
          team_name: json_elem[j].nome_squadra,
          team_owner: json_elem[j].nome_utente,
          score: team_total,
          partial: team_partial
        });
    }

    team_obj = sortTeamsByScore(team_obj);
    printChart(team_obj, "team");

  }else{
    getDriversAndStableFromTeamIdPromise(json_elem[i].id_squadra).then(
      function(drivers_team) {
        drivers_list.push(drivers_team);
        
        recursiveGetDrive(json_elem, drivers_list, all_rows, last_gp_index, i+1);
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
  
  getLastGpIndexPromise().then(
    function(last_gp_index) {
      getFileContentPromise("mod_score.csv").then(
        function(response) {
          let all_rows = response.split(/\r?\n|\r/);
          let drivers_list = [];

          recursiveGetDrive(json_elem, drivers_list, all_rows, last_gp_index, 0);
        }
      );
    }
  );
}

function createDriversChart(json_elem) {
  getLastGpIndexPromise().then(
    function(last_gp_index) {
      getFileContentPromise("score.csv").then(
        function(response) {
          let all_rows = response.split(/\r?\n|\r/);
          let drivers_list = [];

          for(let i = 1; i <= 20; i++) {
            drivers_list.push(all_rows[0].split(",")[i]);
          }

          let driver_total = getDriversTotalScore(drivers_list, all_rows, last_gp_index);
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
      );
    }
  );
}

function createStableChart(json_elem) {
  getLastGpIndexPromise().then(
    function(last_gp_index) {
      getFileContentPromise("score.csv").then(
        function(response) {
          let all_rows = response.split(/\r?\n|\r/);
          let stables_list = [];

          for(let i = 21; i <= 30; i++) {
            stables_list.push(all_rows[0].split(",")[i]);
          }

          let stables_total = getStablesTotalScore(stables_list, all_rows, last_gp_index);
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
      );
    }
  );
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