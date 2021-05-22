window.onload = function() {
  loadTeamsMembersData(getCookie("id_squadra"));
  setInfoIcon();
}


/* -------------------------------------------------
  GETTER
----------------------------------------------------- */

function getNumberOfTimesDriverHasBeenTheBest(drivers_indexes, base_all_rows, last_gp_index) {
  let all_time_best = new Array(5).fill(0);

  console.log(drivers_indexes);

  for(let i = 1; i <= last_gp_index; i++) {
    let gps_score = base_all_rows[i].split(",").slice(1, 21);
    let max = Math.max(...gps_score);

    console.log(max);

    let best_index = gps_score.indexOf(max.toString());

    console.log(best_index);

    if(drivers_indexes.indexOf(best_index) != -1) {
      all_time_best[drivers_indexes.indexOf(best_index)] += 1;
    }
  }

  return all_time_best;
}

function getNumberOfTimesDriverHasBeenTheWorst(drivers_indexes, base_all_rows, last_gp_index) {
  let all_time_worst = new Array(5).fill(0);

  for(let i = 1; i <= last_gp_index; i++) {
    let gps_score = base_all_rows[i].split(",").slice(1, 21);
    let min = Math.min(...gps_score);

    let worst_index = gps_score.indexOf(min.toString());

    if(drivers_indexes.indexOf(worst_index) != -1) {
      all_time_worst[drivers_indexes.indexOf(worst_index)] += 1;
    }
  }

  return all_time_worst;
}

function getNumberOfTimesStableHasBeenTheBest(stable_index, base_all_rows, last_gp_index) {
  let all_time_best = 0;

  for(let i = 1; i <= last_gp_index; i++) {
    let gps_score = base_all_rows[i].split(",").slice(21, 31);
    let max = Math.max(...gps_score);

    let best_index = gps_score.indexOf(max.toString());

    if(stable_index == best_index) {
      all_time_best += 1;
    }
  }

  return all_time_best;
}

function getNumberOfTimesStableHasBeenTheWorst(stable_index, base_all_rows, last_gp_index) {
  let all_time_worst = 0;

  for(let i = 1; i <= last_gp_index; i++) {
    let gps_score = base_all_rows[i].split(",").slice(21, 31);
    let min = Math.min(...gps_score);

    let worst_index = gps_score.indexOf(min.toString());

    if(stable_index == worst_index) {
      all_time_worst += 1;
    }
  }

  return all_time_worst;
}

function getNumberOfTimeDriverHasBeenTheBestInTeam(drivers_indexes, base_all_rows, last_gp_index) {
  let all_time_best = new Array(5).fill(0);
  
  for(let i = 1; i <= last_gp_index; i++) {
    let gps_score = [];

    for(let j = 0; j < drivers_indexes.length; j++) {
      gps_score.push(base_all_rows[i].split(",")[drivers_indexes[j]]);
    }

    let max = Math.max(...gps_score);
    let best_index = gps_score.indexOf(max.toString());

    all_time_best[best_index] += 1;
  }

  return all_time_best;
}

function getNumberOfTimeDriverHasBeenTheWorstInTeam(drivers_indexes, base_all_rows, last_gp_index) {
  let all_time_worst = new Array(5).fill(0);
  
  for(let i = 1; i <= last_gp_index; i++) {
    let gps_score = [];

    for(let j = 0; j < drivers_indexes.length; j++) {
      gps_score.push(base_all_rows[i].split(",")[drivers_indexes[j]]);
    }

    let min = Math.min(...gps_score);
    let worst_index = gps_score.indexOf(min.toString());

    all_time_worst[worst_index] += 1;
  }

  return all_time_worst;
}

function getTimesHasBeenTurboDriver(drivers_indexes, base_all_rows, mod_all_rows, last_gp_index) {
  let td_times = new Array(5).fill(0);
  
  for(let j = 0; j < drivers_indexes.length; j++) {
    
    for(let i = 1; i <= last_gp_index; i++) {
      let base_gp = base_all_rows[i].split(",")[drivers_indexes[j]];
      let mod_gp = mod_all_rows[i].split(",")[drivers_indexes[j]];

      if(base_gp != mod_gp) {
        td_times[j] += 1;
      }
    }
  }

  return td_times;
}

function getCurrentTab() {
let tabs = document.getElementsByClassName("my-nav-item");

for(let i = 0; i < tabs.length; i++) {
  let button = tabs[i].childNodes[1];
  if(button.classList.contains("active")) {
    return tabs[i];
  }
}

return null;
}

function getCurrentPane() {
let panes = document.getElementsByClassName("tab-pane");

for(let i = 0; i < panes.length; i++) {
  if(panes[i].classList.contains("active")) {
    return panes[i];
  }
}

return null;
}

function getTabByDriverSurname(driver) {
let tabs = document.getElementsByClassName("my-nav-item");

console.log(tabs[5]);

for(let i = 0; i < tabs.length; i++) {
  let button = tabs[i].childNodes[1];

  if(!driver.localeCompare(button.id.split("-")[0])) {
    return tabs[i];
  }
}

return null;
}

function getPaneByDriverSurname(driver) {
let panes = document.getElementsByClassName("tab-pane");

for(let i = 0; i < panes.length; i++) {
  if(!driver.localeCompare(panes[i].id.split("-")[0])) {
    return panes[i];
  }
}

return null;
}

function getChartPosition(elem, chart) {
let ordered_chart = stringToArray(getCookie("ordered_" + chart + "s_chart"));
return 1 + ordered_chart.indexOf(elem);
}


/* |>--- | PROMISES | --------------------<| */

function getDriversDataPromise(team_id) {
  return new Promise(function(resolve) {
    $.ajax({
          url: '../Fanta/lib/request.php',
          dataType: 'json',
          data: {get_drivers_data: team_id},
          type: 'POST',
          success: function(response) {
            resolve(response);
          },
          error: function(reject) {
            console.log(reject);
          }
        })
  });
}

function getStableDataPromise(team_id) {
return new Promise(function(resolve) {
  $.ajax({
        url: '../Fanta/lib/request.php',
        dataType: 'json',
        data: {get_stable_data: team_id},
        type: 'POST',
        success: function(response) {
          resolve(response);
        },
        error: function(reject) {
          console.log(reject);
        }
      })
});
}

/* -------------------------------------------------
  SETTER
----------------------------------------------------- */

/* -------------------------------------------------
  PRINT
----------------------------------------------------- */

function printPersonalDriversData(drivers_datas, personal_section) {
  // fa solo i piloti
  for(let i = 0; i < personal_section.length - 1; i++) {
    let rows = personal_section[i].getElementsByClassName('row');
    let drivers_data_property = Object.getOwnPropertyNames(drivers_datas[0]);
    let j;

    for(j = 0; j < rows.length - 1; j++) {
      rows[j].childNodes[3].innerHTML = drivers_datas[i][drivers_data_property[j]];
      if(j == 3 || j == 4) {
        rows[j].childNodes[3].innerHTML += "$";
      }
    }

    // imposto la posizione
    rows[j].childNodes[3].innerHTML = getChartPosition(rows[0].childNodes[3].innerHTML, "driver") + '°';
  }
}

function printPersonalStableData(stable_data, personal_section) {
  let stable_section = personal_section[5];
  let rows = stable_section.getElementsByClassName('row');
  let stable_data_property = Object.getOwnPropertyNames(stable_data);
  let i;

  for(i = 0; i < rows.length - 2; i++) {
    rows[i].childNodes[3].innerHTML = stable_data[stable_data_property[i]];
    if(i == 3 || i == 4) {
      rows[i].childNodes[3].innerHTML += "$";
    }
  }
  // imposto la posizione
  rows[i].childNodes[3].innerHTML = getChartPosition(rows[0].childNodes[3].innerHTML, "stable") + '°';
}

function printChampionshipDriversData(drivers_data) {
  let champ_section = document.getElementsByClassName('championship-data-col');
  
  // faccio i piloti
  for(let i = 0; i < champ_section.length - 1; i++) {
    let rows = champ_section[i].getElementsByClassName('row');
    
    rows[0].getElementsByClassName("text-end")[0].innerHTML = drivers_data[i].total;
    rows[1].getElementsByClassName("text-end")[0].innerHTML = drivers_data[i].average;

    // per gp mettere un tooltip
    //rows[2].getElementsByClassName("text-end")[0].innerHTML = drivers_data[i].best_result.score + " (" + drivers_data[i].best_result.gp.split("-")[0] + ")";
    rows[2].getElementsByClassName("text-end")[0].innerHTML = drivers_data[i].best_result.score + " ";
    rows[2].getElementsByClassName("text-end")[0].appendChild(getFlagElement(drivers_data[i].best_result.gp.split("-")[0], 'left', true));

    rows[3].getElementsByClassName("text-end")[0].innerHTML = drivers_data[i].worst_result.score + " ";
    rows[3].getElementsByClassName("text-end")[0].appendChild(getFlagElement(drivers_data[i].worst_result.gp.split("-")[0], 'left', true));


    //rows[3].getElementsByClassName("text-end")[0].innerHTML = drivers_data[i].worst_result.score + " (" + drivers_data[i].worst_result.gp.split("-")[0] + ")";
    
    rows[4].getElementsByClassName("text-end")[0].innerHTML = drivers_data[i].all_best;
    rows[5].getElementsByClassName("text-end")[0].innerHTML = drivers_data[i].team_best;

    rows[6].getElementsByClassName("text-end")[0].innerHTML = drivers_data[i].td_times;
    

  }
}

function printChampionshipStableData(stable_data) {
  let champ_section = document.getElementsByClassName('championship-data-col');
  
  // faccio la scuderia
  let rows = champ_section[champ_section.length-1].getElementsByClassName('row');
  
  rows[0].getElementsByClassName("text-end")[0].innerHTML = stable_data.total;
  rows[1].getElementsByClassName("text-end")[0].innerHTML = stable_data.average;

  // per gp mettere un tooltip
  rows[2].getElementsByClassName("text-end")[0].innerHTML = stable_data.best_result.score + " "; // + stable_data.best_result.gp.split("-")[0] + ")";
  rows[2].getElementsByClassName("text-end")[0].appendChild(getFlagElement(stable_data.best_result.gp.split("-")[0], 'left', true));
  rows[3].getElementsByClassName("text-end")[0].innerHTML = stable_data.worst_result.score + " "; // + stable_data.worst_result.gp.split("-")[0] + ")";
  rows[3].getElementsByClassName("text-end")[0].appendChild(getFlagElement(stable_data.worst_result.gp.split("-")[0], 'left', true));
  
  rows[4].getElementsByClassName("text-end")[0].innerHTML = stable_data.all_best;
  rows[5].getElementsByClassName("text-end")[0].innerHTML = stable_data.all_worst;

}

function printStatsChart(data, mod_array) {
  let table = document.getElementById('stats-chart');
  let gps_number = data[0].score.length;
  let gps = getGps(false);

  // creo header
  let thead = createStatsChartHeader(stringToArray(getCookie('team')));

  // creo body
  let tbody = document.createElement('tbody');
  
  for(let i = 0; i < gps_number; i++) {
    let tr = document.createElement('tr');

    // creo e appendo la cella con il gp
    let th_scope = document.createElement('th');
    th_scope.setAttribute('scope', 'row');
    th_scope.classList.add('cell-wrapper');
    th_scope.classList.add('d-flex');
    th_scope.classList.add("sticky-col");
    th_scope.setAttribute('style', 'background-color: white');

    let div = document.createElement("div");
    div.classList.add("w-100");
    div.innerHTML = gps[i];

    th_scope.appendChild(div);
    th_scope.appendChild(getFlagElement(gps[i], false, false));

    tr.appendChild(th_scope);

    for(let j = 0; j < data.length; j++) {
      let td = document.createElement('td');
      if(Number.isNaN(data[j].score[i])) {
        td.innerHTML = "-";
      } else {
        td.innerHTML = data[j].score[i];

        if(j < 5) {
          if(data[j].score[i] * 2 == mod_array[j].score[i]) {
            // td
            //td.classList.add('d-flex');
            //td.classList.add('justify-content-center');

            let td_div = document.createElement("span");
            td_div.classList.add('td-color');
            td_div.innerHTML = "(" + mod_array[j].score[i] + ")";
            td.append(td_div);
          } else if(data[j].score[i] * 3 == mod_array[j].score[i]) {
            // md
            //td.classList.add('d-flex');
            //td.classList.add('justify-content-center');
            
            let md_div = document.createElement("span");
            md_div.classList.add('md-color');
            md_div.innerHTML = "(" + mod_array[j].score[i] + ")";
            td.append(md_div);
          }
        }
        
      }

      tr.appendChild(td);
    }

    tbody.appendChild(tr);
  }

  table.appendChild(thead);
  table.appendChild(tbody);

}



/* -------------------------------------------------
  OTHER
----------------------------------------------------- */

function loadTeamsMembersData(team_id) {

  loadPersonalData(team_id);
  
  loadChampionshipData(team_id);
}

function loadPersonalData(team_id) {
  getDriversDataPromise(team_id).then(
    function(drivers_datas) {
        let personal_section = document.getElementsByClassName('personal-data-col');

        printPersonalDriversData(drivers_datas, personal_section);

        // fa la scuderia
        getStableDataPromise(team_id).then(
          function(stable_data) {

            printPersonalStableData(stable_data, personal_section);
        
          }
        );
        
    });
}

function loadChampionshipData(team_id) {
  getFileContentPromise("mod_score.csv").then(
    function(mod_content) {
      getFileContentPromise("score.csv").then(
        function(base_content) {
          getLastGpIndexPromise().then(
            function(last_gp_index) {
              let mod_all_rows = mod_content.split(/\r?\n|\r/);
              let base_all_rows = base_content.split(/\r?\n|\r/);
    
              let team_members_list = stringToArray(getCookie("team"));
              let drivers_list = team_members_list.slice(0, 5);
              let stable = team_members_list[5];

              let drivers_indexes = getDriversIndexes(drivers_list);
              let stable_index = getStableIndex(stable);
    
    
              // totale
              // pilota |--| scuderia
              let drivers_total = getDriversTotalScore(drivers_list, base_all_rows, last_gp_index);
              let stable_total = getStableTotalScore(stable, base_all_rows, last_gp_index);
              
    
              // media
              // pilota |--| scuderia
              let drivers_avg = [];
              let stable_avg;
    
              drivers_total.forEach(element => drivers_avg.push(Math.trunc(element/last_gp_index)));
              stable_avg = Math.trunc(stable_total/last_gp_index);
    
    
              // miglior / peggior risultato
              // pilota |--| scuderia
              let drivers_partial = getDriversPartialPerEachGp(drivers_indexes, base_all_rows, last_gp_index);
              let stable_partial = getStablePartialPerEachGp(stable_index, base_all_rows, last_gp_index);
    
              let drivers_max = [], stable_max;
              let drivers_min = [], stable_min;
    
              for(let i = 0; i < drivers_partial.length; i++) {
                let max_score = Math.max(...drivers_partial[i].score);
                let min_score = Math.min(...drivers_partial[i].score);
    
                let max_gp = getGpByIndex(drivers_partial[i].score.indexOf(max_score) + 1, base_all_rows);
                let min_gp = getGpByIndex(drivers_partial[i].score.indexOf(min_score) + 1, base_all_rows);
    
                drivers_max.push({score: max_score, gp: max_gp});
                drivers_min.push({score: min_score, gp: min_gp});
              }

              let stable_max_score = Math.max(...stable_partial);
              let stable_min_score = Math.min(...stable_partial);
              let stable_max_gp = getGpByIndex(stable_partial.indexOf(stable_max_score) + 1, base_all_rows);
              let stable_min_gp = getGpByIndex(stable_partial.indexOf(stable_min_score) + 1, base_all_rows);
              
              stable_max = {score: stable_max_score, gp: stable_max_gp};
              stable_min = {score: stable_min_score, gp: stable_min_gp};
    

              // # migliore / peggiore in totale
              let all_best = getNumberOfTimesDriverHasBeenTheBest(drivers_indexes, base_all_rows, last_gp_index);
              let all_worst = getNumberOfTimesDriverHasBeenTheWorst(drivers_indexes, base_all_rows, last_gp_index);

              /* TODO */
              let all_best_stable = getNumberOfTimesStableHasBeenTheBest(stable_index, base_all_rows, last_gp_index);
              let all_worst_stable = getNumberOfTimesStableHasBeenTheWorst(stable_index, base_all_rows, last_gp_index);

              setCookie("all_best", all_best, 1);
              setCookie("all_worst", all_worst, 1);
              setCookie("all_best_stable", all_best_stable, 1);
              setCookie("all_worst_stable", all_worst_stable, 1);


              // # migliore / peggiore in squadra
              let team_best = getNumberOfTimeDriverHasBeenTheBestInTeam(drivers_indexes, base_all_rows, last_gp_index);
              let team_worst = getNumberOfTimeDriverHasBeenTheWorstInTeam(drivers_indexes, base_all_rows, last_gp_index);
    
              setCookie("team_best", team_best, 1);
              setCookie("team_worst", team_worst, 1);


              // numero di volte td
              let td_times = getTimesHasBeenTurboDriver(drivers_indexes, base_all_rows, mod_all_rows, last_gp_index)
              

              // creazione oggetti
              let drivers_data = [];
              let stable_data;

              for(let i = 0; i < drivers_total.length; i++) {
                drivers_data.push({
                  total: drivers_total[i],
                  average: drivers_avg[i],
                  best_result: drivers_max[i],
                  worst_result: drivers_min[i],
                  all_best: all_best[i],
                  all_worst: all_worst[i],
                  team_best: team_best[i],
                  team_worst: team_worst[i],
                  td_times: td_times[i]
                });
              }

              stable_data = {
                total: stable_total,
                average: stable_avg,
                best_result: stable_max,
                worst_result: stable_min,
                all_best: all_best_stable,
                all_worst: all_worst_stable
              }

              printChampionshipDriversData(drivers_data);

              printChampionshipStableData(stable_data);
    
            }
          );
        }
      );
      
    }
  );
}

function changeNumberOfTimes(new_elem) {
  let old_elem = new_elem.parentNode.parentNode.parentNode.getElementsByClassName("dropdown-toggle")[0];
  let tmp = old_elem.innerHTML;

  let text_end = old_elem.parentNode.parentNode.parentNode.getElementsByClassName("col-6")[1];
  let flag_best = new_elem.innerHTML.search("migliore");
  let flag_team = new_elem.innerHTML.search("team");

  let driver = new_elem.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.id.split("-")[0];
  let driver_index = stringToArray(getCookie("team")).indexOf(driver);
  // scambio new dropdown
  old_elem.innerHTML = new_elem.innerHTML;
  new_elem.innerHTML = tmp;

  if(flag_best == -1) {
    // peggiore -> migliore
    if(flag_team == -1) {
      // all
      text_end.innerHTML = stringToArray(getCookie("all_worst"))[driver_index];
    } else {
      text_end.innerHTML = stringToArray(getCookie("team_worst"))[driver_index];
    }
  } else {
    // migliore
    if(flag_team == -1) {
      // all
      text_end.innerHTML = stringToArray(getCookie("all_best"))[driver_index];
    } else {
      text_end.innerHTML = stringToArray(getCookie("team_best"))[driver_index];
    }
  }


  
}

function loadTabPane(elem) {
  let current_tab = getCurrentTab();
  let new_tab = getTabByDriverSurname(removeSpaces(elem.innerHTML).replace(" ", "_"));

  let current_pane = getCurrentPane();
  let new_pane = getPaneByDriverSurname(removeSpaces(elem.innerHTML).replace(" ", "_"));


  // sistemi i tab
  current_tab.childNodes[1].classList.remove('active');
  current_tab.classList.add('d-none');
  current_tab.classList.add('d-sm-block');

  new_tab.childNodes[1].classList.add('active');
  new_tab.classList.remove('d-none');
  new_tab.classList.remove('d-sm-block');


  // sistemo i pane
  current_pane.classList.remove('show');
  current_pane.classList.remove('active');

  new_pane.classList.add('show');
  new_pane.classList.add('active');
  
}

function getLastGpIndex(all_rows, gp_location) {
  for(let i = 1; i < all_rows.length; i++){
    if(all_rows[i].split("-")[0].localeCompare(gp_location) == 0) {
      return i;
    }
  }

  return -1;
}

function createStatsChart() {
  let team = stringToArray(getCookie("team"));

  getFileContentPromise("score.csv").then(
    function(content){
      getFileContentPromise("mod_score.csv").then(
        function(mod_content) {
          let all_rows = content.split(/\r?\n|\r/);
          let mod_all_rows = mod_content.split(/\r?\n|\r/);
          let team_indexes = getDriversIndexes(team.slice(0,5));
          team_indexes.push(getStableIndex(team[5]));

          let modifier_array = [];
          let last_gp = getLastGpIndex(all_rows, getLastGpLocation());

          let data = getDriversPartialPerEachGp(team_indexes.slice(0, 5), all_rows, all_rows.length-1);

          //for(let i = 0; i < team_indexes.length - 1; i++) {
            modifier_array = getDriversPartialPerEachGp(team_indexes.slice(0,5), mod_all_rows, last_gp);
          //}

          data.push({
            stable: team_indexes[5],
            score: getStablePartialPerEachGp(team_indexes[5], all_rows, all_rows.length-1)
          });
    
          printStatsChart(data, modifier_array);
        }
      )
      
    }
  );

}

function createStatsChartHeader(team) {
  let thead = document.createElement('thead');
  
  let tr = document.createElement('tr');
  let th = new Array(team.length + 1);
  
  th[0] = document.createElement('th');

  th[0].scope = "col";
  th[0].innerHTML = "GP";
  tr.appendChild(th[0]);

  for(let i = 0; i < team.length; i++) {
    th[i+1] = document.createElement("th");
    th[i+1].scope = "col";
    th[i+1].innerHTML = team[i];
    th[i+1].classList.add('cell-wrapper');
    th[i+1].classList.add('cell-sizing');
    tr.appendChild(th[i+1]);
  }
  
  thead.appendChild(tr);
  return thead;
}