let livery = ['#00d2be' /*Mercedes*/, '#0600ef' /*Red Bull*/, '#ff8700' /*McLaren*/, '#dc0000' /*Ferrari*/, '#006F62' /*Aston Martin*/,
              '#0090FF' /*Alpine*/, '#2B4562' /*Alpha Tauri*/, '#900000' /*Alfa Romeo*/, '#005AFF' /*Williams*/, '#FFFFFF' /*Haas*/];

let drivers = ['Hamilton', 'Bottas', 'Verstappen', 'Perez', 'Ricciardo', 'Norris', 'Leclerc', 'Sainz', 'Vettel', 'Stroll',
                'Alonso', 'Ocon', 'Gasly', 'Tsunoda', 'Raikkonen', 'Giovinazzi', 'Russel', 'Latifi', 'Schumacher', 'Mazepin']

let stables = ['Mercedes', 'Red Bull', 'McLaren', 'Ferrari', 'Aston Martin', 'Alpine', 'AlphaTauri', 'Alfa Romeo', 'Williams', 'Haas'];

/* TODO:
- settare next_gp in base alla data
*/



/* -------------------------------------------------
  COOKIES
----------------------------------------------------- */

function setCookie(name, value, days) {
  var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

/* -------------------------------------------------
  SETTER
----------------------------------------------------- */

function setInfoIcon() {
  let container = document.getElementById('info-icon-container');
  let box = document.createElement('box-icon');

  box.setAttribute('id', 'info-icon');
  box.setAttribute('name', 'info-circle');
  box.setAttribute('color', '#d90000');
  box.setAttribute('data-bs-toggle', 'tooltip');
  box.setAttribute('data-bs-placement', 'right');
  box.setAttribute('title', 'Ruota il dispositivo per vedere meglio');
  box.classList.add("h-100");
  box.classList.add("ms-1");
  box.classList.add("d-md-none");

  var tooltip = new bootstrap.Tooltip(box);

  container.appendChild(box);
}

/* -------------------------------------------------
  GETTER
----------------------------------------------------- */

function getGpByIndex(index, csv_data) {
  for(let i = 1; i < csv_data.length; i++) {
    if(i == index) {
      return csv_data[i][0];
    }
  }

  return "";
}

function getGps(date_flag) {
  let gps = [];

  for(let gp of stringToArray(getCookie("gps"))) {
    date_flag ? gps.push(gp) : gps.push(gp.split("-")[0]);
  }
  return gps;
}

function getTotalPerEachGP(drivers_indexes, csv_data, last_gp_index) {
  let total_arr = [];
  let driver_arr = [];

  for(let j = 0; j < drivers_indexes.length; j++) {
    driver_arr = [];
    // scorro i gp
    for(let i = 1; i < last_gp_index+1; i++) {
      let val = castScore(csv_data[i][drivers_indexes[j]]);
      if(i > 1) {
        val += castScore(driver_arr[i-2]);
      }

      driver_arr.push(val);
    }
    
    total_arr.push(driver_arr);
  }

  return total_arr;
}

function getLastGpLocation() {
  return getCookie("last_gp").split("-")[0];
}

function getLastGpDate() {
  return getCookie("last_gp").split("-")[1];
}

function getNextGpLocation() {
  return getCookie("next_gp").split("-")[0];
}

function getNextGpDate() {
  return getCookie("next_gp").split("-")[1];
}

function getGpDate(gp_location) {
  return getGps(true)[getGps(false).indexOf(gp_location)].split("-")[1];
}

function getFlagElement(gp_location, position, tooltip_flag) {
  let tag = document.createElement('img');
  tag.setAttribute('src', '../Fanta/images/flags/' + gp_location.toLowerCase() + '.png');
  tag.classList.add('flag-icon');

  if(tooltip_flag){
    tag.setAttribute('data-bs-toggle', 'tooltip');
    tag.setAttribute('data-bs-placement', position);
    tag.title = gp_location + " - " + getGpDate(gp_location);
    var tooltip = new bootstrap.Tooltip(tag);
  }
  

  return tag;
}

function getLastGpTeamScore(drivers_indexes, stable_index) {
  let score = scoreConverterToArray(getCookie('mod_scores_data'), 31);
  let last_gp_index = getLastGpIndex(score);

  let total = castScore(score[last_gp_index][castScore(stable_index) + 20]);
  
  for(let i = 0; i < drivers_indexes.length; i++) {
    total += castScore(score[last_gp_index][drivers_indexes[i]]);
  }


  return total;
}


/* |>--- | DRIVERS FUNCTIONS | --------------------<| */

function getPersonalDriversFromCards() {
  let arr = [], cards = document.getElementsByClassName('card');
  
  for(let i = 0; i < 5; i++) {
    arr.push(cards[i].id.split('-')[0]);
  }

  return arr;
}

function getPersonalDrivers() {
  let team = getCookie('team');
  return team.split(",").slice(0,5);
}

function getDriverIndex(driver) {

  for(let i = 0; i < drivers.length; i++) {

    if(!drivers[i].localeCompare(driver)) {
      return i+1;
    }
      
  }
}

function getDriversIndexes(drivers_list) {
  let drivers_indexes = [];

  for(let i = 0; i < drivers_list.length; i++) {
    drivers_indexes.push(getDriverIndex(drivers_list[i]));
  }

  return drivers_indexes;
}

function getDriversTotalScore(drivers_list, csv_data, last_gp_index) {
  let drivers_total = new Array(drivers_list.length).fill(0);
  let drivers_index_list = getDriversIndexes(drivers_list);

  for(let j = 0; j < drivers_list.length; j++){
    for(let i = 1; i <= last_gp_index; i++){
      let tmp_val = csv_data[i][drivers_index_list[j]];
      drivers_total[j] += castScore(tmp_val);
    }
  }

  return drivers_total;
}

function getDriversLastScore(drivers_list, csv_data, last_gp_index) {
  let drivers_last = [0, 0, 0, 0, 0];
  let drivers_index_list = getDriversIndexes(drivers_list);

  for(let j = 0; j < drivers_list.length; j++){
    drivers_last[j] = csv_data[last_gp_index][drivers_index_list[j]];
  }

  return drivers_last;
}

function getDriversPartialPerEachGp(drivers_indexes, csv_data, last_gp_index) {
  let partial_results = [];

  for(let i = 0; i < drivers_indexes.length; i++) {
    partial_results.push({
      driver: drivers_indexes[i],
      score: getDriverPartialPerEachGp(drivers_indexes[i], csv_data, last_gp_index)
    });
  }

  return partial_results;
}

function getDriverPartialPerEachGp(driver_index, csv_data, last_gp_index) {
  let partial_result = [];

  for(let i = 1; i <= last_gp_index; i++) {
    partial_result.push(castScore(csv_data[i][driver_index]));
  }

  return partial_result;
}


/* |>--- | STABLES FUNCTIONS | --------------------<| */

function getPersonalStableFromCard() {
  return document.getElementsByClassName('card')[5].id.split('-')[0]
}

function getPersonalStable() {
  let team = getCookie('team');
  return team.split(",")[5];
}

function getStableIndex(stable) {
  for(let i = 0; i < stables.length; i++) {
    if(!stables[i].localeCompare(stable)) {
      return i+1;
    }
  }

  return -1;
}

function getStableTotalScore(stable, csv_data, last_gp_index) {
  let stable_total = 0;
  let stable_index = getStableIndex(stable);

  for(let i = 1; i <= last_gp_index; i++){
    let tmp_val = csv_data[i][20 + stable_index];
    stable_total += castScore(tmp_val);
  }

  return stable_total;
}

function getStablesTotalScore(stable_list, csv_data, last_gp_index) {
  let arr = [];
  for(let i = 0; i < stable_list.length; i++) {
    arr.push(getStableTotalScore(stable_list[i], csv_data, last_gp_index));
  }

  return arr;
}

function getStableLastScore(stable, csv_data, last_gp_index) {
  let stable_index = getStableIndex(stable);
  return csv_data[last_gp_index][20 + stable_index];
}

function getStablePartialPerEachGp(stable_index, csv_data, last_gp_index) {
  let partial_result = [];

  for(let i = 1; i <= last_gp_index; i++) {
    partial_result.push(castScore(csv_data[i][20 + stable_index]));
  }

  return partial_result;
}


/* |>--- | PROMISES | --------------------<| */

function getLastGpIndexPromise() {
  return new Promise(function(resolve, reject) {
    $.ajax({
          url: '../Fanta/data/score.csv',
          dataType: 'text',
        }).done(function(response){
          let all_rows = response.split(/\r?\n|\r/);
          let i = 0;

          while(all_rows[i].split(",")[1] != "") {
            i++;
          }

          resolve(i-1);
        });
  });
}

function getLastGpIndex(csv_data) {
  let i = 0;
  //let data = scoreConverterToArray(csv_data);
  while(csv_data[i][1] != "") { i++; }

  return (i-1);
}

function getGpIndex(gp, csv_data) {
  let i = 1;
  while(csv_data[i][0].split("-")[0] != gp) { i++; }

  return i;
}

function scoreConverterToArray(score, row_len) {
  score = score.split(",");
  let data = [];
  
  let len = score.length/row_len;

  let i = 0;
  while(i < len - 1) {
    data.push(score.slice(0, row_len));
    score = score.slice(row_len);

    i++;
  }

  return data;
}


function getMegaDriverInfoPromise() {
  return new Promise(function(resolve, reject) {
      $.ajax({
          type: "POST",
          url: '../Fanta/lib/request.php',
          data: { get_md_info : "" },
          datatype: 'json',
          success: function(response) {
            resolve(response)
          },
          error: function(err) {
            reject(err)
          }
      });
  });
}
/*
function getDriverScorePromise(driver, filename) {
  return new Promise(function(resolve, reject) {
    $.ajax({
      type: "POST",
      url: 'http://ifantastici4.redirectme.net:9001/Programmi/Fanta/data/' + filename,
      dataType: 'text',
      success: function(response) {
        let all_rows = response.split(/\r?\n|\r/);
        let driver_list = all_rows[0].split(",");
        let driver_index = getDriverIndex(driver);

        /*let data_score = getCookie('scores_data');
        let last_gp_score = data_score[last_gp_index][driver_index];

        resolve(last_gp_score);

        let last_gp_index = getLastGpIndex(getCookie('scores_data'));


        getLastGpIndexPromise().then(
          function (last_gp_index) {
            let last_gp_score = all_rows[last_gp_index].split(",")[driver_index];

            console.log(castScore(last_gp_score));
            resolve(castScore(last_gp_score));
          });

        },
        error: function(err) {
          reject(err)
        }
      });
    });
}*/

function getDriverScore(driver, gp_index, mode) {
  let driver_index = getDriverIndex(driver);
  if(typeof driver_index === 'undefined') return null;
  
  let score = mode == 'score' ? scoreConverterToArray(getCookie('scores_data'), 31) : scoreConverterToArray(getCookie('mod_scores_data'), 31);
  
  return castScore(score[gp_index][driver_index]);
}

function getTeamsInfoPromise() {
  return new Promise(function(resolve, reject) {
    $.ajax({
      type: "POST",
      url: '../Fanta/lib/request.php',
      datatype: 'json',
      data: { get_teams : "" },
      success: function(response){
        resolve(response)
      },
      error: function(err) {
        reject(err)
      }
    });
  });
}
/*
function getLastGpBaseScorePromise() {
  return new Promise(function(resolve, reject) {
    $.ajax({
          url: '../Fanta/data/score.csv',
          dataType: 'text',
        }).done(function(response){
          let all_rows = response.split(/\r?\n|\r/);

          let last_gp_index = getLastGpIndex(scoreConverterToArray(getCookie('scores_data')));
          if(last_gp_index == 0) return null;
          else return all_rows[last_gp_index];
        });
  });
}*/

function getLastGpBaseScore() {
  let score = scoreConverterToArray(getCookie('scores_data'), 31);
  let last_gp_index = getLastGpIndex(score);
  if(last_gp_index == 0) return null;
  else return score[last_gp_index];
}

function getLastGpModScore() {
  let score = scoreConverterToArray(getCookie('mod_scores_data'), 31);
  let last_gp_index = getLastGpIndex(score);
  if(last_gp_index == 0) return null;
  else return score[last_gp_index];
}

function getFileContentPromise(filename) {
  return new Promise(function(resolve){
      $.ajax({
        url: '../Fanta/data/' + filename,
        dataType: 'text',
      }).done(function(response){
        resolve(response);
      });
  });
}

function getDriversAndStableFromTeamIdPromise(team_id) {
  return new Promise(function(resolve){
    $.ajax({
      type: "POST",
      url: '../Fanta/lib/request.php',
      data: { get_drivers_by_team_id : team_id },
      datatype: 'json',
      success: function(response) {
        resolve(JSON.parse(response));
      },
      error: function(err) {
        reject(err)
      }
    });
  });
  
}


/* -------------------------------------------------
  OTHER FUNCTIONS
----------------------------------------------------- */

function sumArray(arr){
  let tmp = castScore(arr[0]);
  for(let i = 1; i < arr.length; i++) {
    tmp += castScore(arr[i]);
  }

  return tmp;
}

function castScore(score) {
  if(score == '') return 0;
  return parseFloat(score);
}

function export_csv(arrayHeader, arrayData, delimiter, fileName) {
  let header = arrayHeader.join(delimiter) + '\n';
  let csv = header;
  arrayData.forEach( array => {
    csv += array.join(delimiter)+"\n";
  });

  let csvData = new Blob([csv], { type: 'text/csv' });
  let csvUrl = URL.createObjectURL(csvData);

  let hiddenElement = document.createElement('a');
  hiddenElement.href = csvUrl;
  hiddenElement.target = '_blank';
  hiddenElement.download = fileName; // + '.csv';
  hiddenElement.click();
}

function writeRowIn(filename, row, index) {
  // leggo filename
  $.ajax({
    url: 'http://ifantastici4.redirectme.net:9001/Programmi/Fanta/data/' + filename,
    dataType: 'text',
  }).done(function(response){
    let all_rows = response.split(/\r?\n|\r/);

    let arrayHeader = all_rows[0].split(",");
    let arrayData = [];

    for(let i = 1; i < all_rows.length; i++) {
      if(i == index)
      arrayData.push(row);
      else
      arrayData.push(all_rows[i].split(","));
    }
    
    // riscrivo tutto dentro filename
    export_csv(arrayHeader, arrayData, ",", filename);
    console.log(filename + " aggiornato!");
  });

}

// carico i file da score.csv a mod_score.csv, mettendo i valori in base a td e md
function oldLoadScore() {
  // ciclo tutte le squadre
  // - per ogni ciclo prendo td e md

  getTeamsInfoPromise().then(
    function(teams_info) {
      let score = getLastGpBaseScore();
      let last_gp_index = getLastGpIndex(scoreConverterToArray(getCookie('scores_data'), 31));

      if(score == null) return;

      let elements = JSON.parse(teams_info);  // squadre
      let teams_info_obj = [];
      let new_score = new Array(31);

      new_score[0] = score[0];

      for(let i = 0; i < elements.length; i++) {
        let team = JSON.parse(elements[i]);
        teams_info_obj.push(team)

      // inserisce i piloti
        for(let j = 0; j < team.drivers.length; j++){
          if(castScore(team.turbo_driver) == castScore(team.drivers[j])) {
            // turbo driver
            new_score[team.drivers[j]] = 2*castScore(score[team.drivers[j]]);
          } else if(castScore(team.mega_driver) == castScore(team.drivers[j])) {
            // mega driver
            new_score[team.drivers[j]] = 3*castScore(score[team.drivers[j]]);
          }else {
            new_score[team.drivers[j]] = castScore(score[team.drivers[j]]);
          }
        }
      }

      // inserisco le scuderie
      for(let i = 0; i < 10; i++) {
        new_score[i+21] = castScore(score[i+21]);
      }

      writeRowIn("mod_score.csv", new_score, last_gp_index);

//      console.log(teams_info_obj)

      let last_score_JSON = [];
      for(let i = 0; i < teams_info_obj.length; i++) {
        let last_score = getLastGpTeamScore(teams_info_obj[i].drivers, teams_info_obj[i].id_scuderia);
        last_score_JSON.push(JSON.parse('{"id_squadra": "' + teams_info_obj[i].id_squadra + '", "last_score": "' + last_score + '"}'))
      }

      console.log(last_score_JSON);

      updateTeamsScore(last_score_JSON);

      

/*
      LastGpBaseScorePromise().then(
        function(score) {

          
        });*/
    });
}

function loadScore(load_score_obj) {
  let gp_index = getGpIndex(load_score_obj.gp.split('-')[0], scoreConverterToArray(getCookie('scores_data'), 31));
  let score_row = [load_score_obj.gp];

  score_row = score_row.concat(load_score_obj.drivers_score);
  score_row = score_row.concat(load_score_obj.stables_score);

  // scrivo i punteggi in score.csv
  writeRowIn('score.csv', score_row, gp_index);

  getTeamsInfoPromise().then(
    function(teams_info) {
      if(score_row == null) return;

      let teams = JSON.parse(teams_info);  // squadre
      let teams_info_obj = [];
      let new_score = new Array(31);

      new_score[0] = score_row[0];

      for(let i = 0; i < teams.length; i++) {
        let team = JSON.parse(teams[i]);
        teams_info_obj.push(team);

      // inserisce i piloti
        for(let j = 0; j < team.drivers.length; j++){
          if(castScore(team.turbo_driver) == castScore(team.drivers[j])) {
            // turbo driver
            new_score[team.drivers[j]] = 2*castScore(score_row[team.drivers[j]]);
          } else if(castScore(team.mega_driver) == castScore(team.drivers[j])) {
            // mega driver
            new_score[team.drivers[j]] = 3*castScore(score_row[team.drivers[j]]);
          }else {
            new_score[team.drivers[j]] = castScore(score_row[team.drivers[j]]);
          }
        }
      }

      // inserisco le scuderie
      for(let i = 0; i < 10; i++) {
        new_score[i+21] = castScore(score_row[i+21]);
      }

      // scrivo i punteggi in mod_score.csv
      writeRowIn("mod_score.csv", new_score, gp_index);

      console.log(teams_info_obj)

      let teams_score = [new_score[0]];
      for(let i = 0; i < teams_info_obj.length; i++) {
        let tmp_score = 0;
        for(let j = 0; j < teams_info_obj[i].drivers.length; j++) {
          tmp_score += castScore(new_score[teams_info_obj[i].drivers[j]]);
        }
        console.log(new_score[parseInt(teams_info_obj[i].id_scuderia) + 20])
        tmp_score += castScore(new_score[parseInt(teams_info_obj[i].id_scuderia) + 20]);

        teams_score.push(tmp_score);
      }

      console.log(teams_score)

      writeRowIn("teams_score.csv", teams_score, gp_index);



      let last_score_JSON = [];
      for(let i = 0; i < teams_info_obj.length; i++) {
        let last_score = getLastGpTeamScore(teams_info_obj[i].drivers, teams_info_obj[i].id_scuderia);
        last_score_JSON.push(JSON.parse('{"id_squadra": "' + teams_info_obj[i].id_squadra + '", "last_score": "' + last_score + '"}'))
      }

      updateTeamsScore(last_score_JSON);

      setCookie('scores_data', null, 1);
      setCookie('mod_scores_data', null, 1);
    }
  )
}

function updateTeamsScore(last_score) {
  let today_string = today().getDate() + '/' + (today().getMonth()+1) + '/' +  today().getFullYear();
  $.ajax({
    type: 'POST',
    url: 'http://ifantastici4.redirectme.net:9001/Programmi/Fanta/lib/request.php',
    data: { update_teams_score: last_score,
            last_gp_date: getLastGpDate() }
  }).done(function(response){
      console.log(response)
  });
}

function isCharacterALetter(char) {
  return (/[a-zA-Z]/).test(char)
}

function removeSpaces(string) {
  let n_str = "";
  let first_char = string.length;

  for(let i = 0; i < string.length; i++){
    if(string.charAt(i) >= 'A' && string.charAt(i) <= 'Z' || string.charAt(i) >= 'a' && string.charAt(i) <= 'z') {
      n_str += string.charAt(i);
      if(first_char == string.length)
        first_char = i;
    } else if(i < string.length - 1 && isCharacterALetter(string.charAt(i+1)) && first_char < i) {
      // spazio interno
      n_str += string.charAt(i);
    }
  }

  return n_str;
}

function stringToArray(string) {
  if(string.charAt(0) == '[') {
    string = string.substring(1, string.length - 1);
  }
  return string.split(",");
}

function arrayToString() {
  
}

function setBorder(id, elem) {
  elem.setAttribute('border-left', '3px solid '+getLiveryByDriverId(id));
}

function getLiveryByDriverId(driver_id) {
  return livery[Math.ceil(driver_id/2) - 1];
}

function getLiveryByStableId(stable_id) {
  return livery[stable_id-1];
}


/* |>--- | TIME FUNCTIONS | --------------------<| */

function today() {
  var today = new Date();
  var dd = String(today.getDate()).padStart(2, '0');
  var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
  var yyyy = today.getFullYear();

  today = dd + '/' + mm + '/' + yyyy;
  return formatDate(today);
}

function formatDate(string_date){
  let day = string_date.split('/')[0];
  let month = string_date.split('/')[1];
  let year = string_date.split('/')[2];

  let date = new Date(year + "-" + month + "-" + day);
  return date;
}

function diffDate(date1, date2) {
  const diff_time = Math.abs(date1 - date2);
  const diff_days = Math.ceil(diff_time / (1000 * 60 * 60 * 24)) - 1; // -1 perchè le modifiche si fermano a sabato

  return diff_days;
}



/* |>--- | TMP FUNCTIONS | --------------------<| */

function printOnConsoleTeamHistory(team_id) {

}