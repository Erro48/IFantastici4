
/* -------------------------------------------------
  ONLOAD
----------------------------------------------------- */

window.onload = function() {
  loadLegend();
}

function loadLegend() {
    // prendo i miei 5 piloti
    getLastGpIndexPromise().then(function(last_gp_index) {
      getFileContentPromise("score.csv").then(function(file_content) {
        let all_rows = file_content.split(/\r?\n|\r/);
        let drivers_list = getCookie("team").split(",").slice(0, 5);
        let total = getDriversTotalScore(drivers_list, all_rows, last_gp_index);
        let drivers_obj = [];

        for(let i = 0; i < drivers_list.length; i++) {
          drivers_obj.push({
            driver: drivers_list[i],
            score: total[i]
          });
        }

        drivers_obj = sortDriversByScore(drivers_obj);
        printLegend(drivers_obj);

        drawGraph(all_rows, last_gp_index);
      });
    });
}

function drawGraph(all_rows, last_gp_index) {
  let gps = getGps(all_rows, false);
  let drivers_list = getCookie("team").split(",").slice(0, 5);
  let total_per_each_gp = getTotalPerEachGP(getDriversIndexes(drivers_list), all_rows, last_gp_index);
  let drivers_style = getDriversStyle(getDriversIndexes(drivers_list));

  let datasets = [];
  for(let i = 0; i < total_per_each_gp.length; i++) {
    datasets.push({
      label: drivers_list[i],
      data: total_per_each_gp[i],
      fill: false,
      borderColor: drivers_style[i].color,
      borderDash: drivers_style[i].style,
      tension: 0.01,
      pointRadius: 3,
      pointBackgroundColor: drivers_style[i].color
    });
  }

  const labels = gps;
  const data = {
    labels: labels,
    datasets: datasets
  };
  
  var ctx = document.getElementById('stats-graph');

  ctx.height = 200;

  var myChart = new Chart(ctx, {
      type: 'line',
      data: data,
      options: {
          scales: {
              y: {
                  beginAtZero: true
              }
          },
          plugins: {
            legend: {
                labels: {
                    // This more specific font property overrides the global property
                    font: {
                        size: 14
                    }
                }
            }
        },
        responsive: true,
        maintainAspectRatio: false
      }
  });

}

function sortDriversByScore(object) {
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

function printLegend(drivers_obj) {
  let elem = document.getElementById("legend-content");
  let ol = document.createElement("ol");

  ol.classList.add("list-group");
  ol.classList.add("list-group-numbered");
  ol.classList.add("flex-xxl-column");
  ol.classList.add("flex-column");
  ol.classList.add("flex-row");

  for(let i = 0; i < drivers_obj.length; i++) {
    let li = document.createElement("li");
    let outer_div = document.createElement("div");
    let inner_div = document.createElement("div");
    let span = document.createElement("span");


    li.classList.add("list-group-item");
    li.classList.add("d-flex");
    li.classList.add("justify-content-between");
    li.classList.add("align-items-start");
    li.classList.add("flex-fill");

    outer_div.classList.add("ms-2");
    outer_div.classList.add("me-auto");

    inner_div.classList.add("fw-bold");

    span.classList.add("badge");
    span.classList.add("bg-primary");
    span.classList.add("rounded-pill");

    
    inner_div.innerHTML = drivers_obj[i].driver;
    span.innerHTML = drivers_obj[i].score;

    outer_div.appendChild(inner_div);
    li.appendChild(outer_div);
    li.appendChild(span);
    ol.appendChild(li);
  }

  elem.appendChild(ol);
}

function getDriversStyle(drivers_indexes) {
  let style_obj = [];
  let clr_flag = "";

  for(let i = 0; i < drivers_indexes.length; i++) {
    let style;


    let color = livery[parseInt((drivers_indexes[i] - 1)/2)];
    
    if(!clr_flag.localeCompare(color)) {
      // se sono uguali
      style = [5, 5];
    } else {
      style = [0, 0];
    }
    
    clr_flag = color;

    style_obj.push({
      color: color,
      style: style
    });
  }

  return style_obj;
}