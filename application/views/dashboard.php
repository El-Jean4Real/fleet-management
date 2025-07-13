<!-- dashboard.php -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Dashboard</h1>
      </div>
      <div class="col-sm-6 text-right">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </div>
    </div>
  </div>
</div>
	<!-- ?? Résumés classiques -->
    <div class="row">
      <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
          <div class="inner">
            <h3><?= $dashboard['tot_vehicles']; ?></h3>
            <p>Total Vehicles</p>
          </div>
          <div class="icon"><i class="fas fa-truck"></i></div>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
          <div class="inner">
            <h3><?= $dashboard['tot_drivers']; ?></h3>
            <p>Total Drivers</p>
          </div>
          <div class="icon"><i class="fas fa-user-tie"></i></div>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
          <div class="inner">
            <h3><?= $dashboard['tot_customers']; ?></h3>
            <p>Total Customers</p>
          </div>
          <div class="icon"><i class="fas fa-user"></i></div>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
          <div class="inner">
            <h3><?= $dashboard['tot_today_trips']; ?></h3>
            <p>Today Trips</p>
          </div>
          <div class="icon"><i class="fas fa-road"></i></div>
        </div>
      </div>
    </div>

<div class="card mt-3">
  <div class="card-header bg-gradient-primary text-white">
    Objectifs Réalisés (Hebdomadaire)
  </div>
  <div class="card-body">
    <canvas id="objectifChart" height="60"></canvas>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById("objectifChart").getContext("2d");

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: <?= $graph_labels ?>,
      datasets: [
        {
          label: "Objectif (XAF)",
          backgroundColor: "#00c0ef",
          data: <?= $graph_objectif ?>
        },
        {
          label: "Réalisé (XAF)",
          backgroundColor: "#00a65a",
          data: <?= $graph_realise ?>
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
});
</script>

<section class="content">
  <div class="container-fluid">

    
	<!-- ?? Graphe Objectifs Hebdo -->
    <div class="row">
      <div class="col-md-12">
        <div class="card card-outline card-info">
          <div class="card-header">
            <h3 class="card-title">Objectives Overview (Vehicle - Weekly)</h3>
          </div>
          <div class="card-body">
            <canvas id="objectifChart" height="120"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- ?? Tableaux Objectifs Hebdo et Mensuels côte à côte -->
    <div class="row">
      <!-- Objectifs Hebdomadaires -->
      <div class="col-md-6">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">📅 Objectifs Hebdomadaires (par véhicule)</h3>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-hover text-nowrap">
              <thead class="bg-light">
                <tr>
                  <th>#</th>
                  <th>Véhicule</th>
                  <th>Période</th>
                  <th>Objectif</th>
                  <th>Réalisé</th>
                  <th>Taux</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($objectifs_hebdo)): $i = 1; ?>
                  <?php foreach ($objectifs_hebdo as $obj): ?>
                    <?php 
                      $total = $obj['montant_realise'] + $obj['montant_manuel'];
                      $taux     = ($obj['montant_objectif'] > 0) ? round(($total / $obj['montant_objectif']) * 100, 2) : 0;
                      $color = ($taux >= 90) ? 'bg-success' : (($taux >= 50) ? 'bg-warning' : 'bg-danger');
                      $status = ($taux >= 100) ? '🎯 Atteint' : (($taux < 50) ? '⚠️ En retard' : 'En cours');
                    ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= $obj['vehicule_nom']; ?></td>
                      <td><?= date('d/m/Y', strtotime($obj['periode_debut'])) . ' → ' . date('d/m/Y', strtotime($obj['periode_fin'])); ?></td>
                      <td><?= number_format($obj['montant_objectif'], 0, ',', ' '); ?> XAF</td>
                      <td><?= number_format($total, 0, ',', ' '); ?> XAF</td>
                      <td>
                        <div class="progress progress-xs">
                          <div class="progress-bar <?= $color ?>" style="width: <?= $taux; ?>%"></div>
                        </div>
                        <span class="badge <?= $color ?>"><?= $taux; ?>%</span>
                      </td>
                      <td><?= $status; ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="7">Aucun objectif hebdomadaire trouvé.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Objectifs Mensuels -->
      <div class="col-md-6">
        <div class="card card-outline card-success">
          <div class="card-header">
            <h3 class="card-title">🗓️ Objectifs Mensuels (par véhicule)</h3>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-hover text-nowrap">
              <thead class="bg-light">
                <tr>
                  <th>#</th>
                  <th>Véhicule</th>
                  <th>Période</th>
                  <th>Objectif</th>
                  <th>Réalisé</th>
                  <th>Taux</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($objectifs_mensuel)): $i = 1; ?>
                  <?php foreach ($objectifs_mensuel as $obj): ?>
                    <?php 
                      $total = $obj['montant_realise'] + $obj['montant_manuel'];
                      $taux = ($obj['montant_objectif'] > 0) ? round(($total / $obj['montant_objectif']) * 100, 2) : 0;
                      $color = ($taux >= 90) ? 'bg-success' : (($taux >= 50) ? 'bg-warning' : 'bg-danger');
                      $status = ($taux >= 100) ? '🎯 Atteint' : (($taux < 50) ? '⚠️ En retard' : 'En cours');
                    ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= $obj['vehicule_nom']; ?></td>
                      <td><?= date('d/m/Y', strtotime($obj['periode_debut'])) . ' → ' . date('d/m/Y', strtotime($obj['periode_fin'])); ?></td>
                      <td><?= number_format($obj['montant_objectif'], 0, ',', ' '); ?> XAF</td>
                      <td><?= number_format($total, 0, ',', ' '); ?> XAF</td>
                      <td>
                        <div class="progress progress-xs">
                          <div class="progress-bar <?= $color ?>" style="width: <?= $taux; ?>%"></div>
                        </div>
                        <span class="badge <?= $color ?>"><?= $taux; ?>%</span>
                      </td>
                      <td><?= $status; ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="7">Aucun objectif mensuel trouvé.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ?? SCRIPT GRAPHE -->
<script>
const ctxObj = document.getElementById('objectifChart').getContext('2d');
const objectifChart = new Chart(ctxObj, {
    type: 'bar',
    data: {
        labels: <?= $graph_labels ?>,
        datasets: [
            {
                label: 'Target',
                data: <?= $graph_objectif ?>,
                backgroundColor: '#007bff'
            },
            {
                label: 'Achieved',
                data: <?= $graph_realise ?>,
                backgroundColor: '#28a745'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            x: { stacked: false, ticks: { autoSkip: false } },
            y: { beginAtZero: true }
        }
    }
});
</script>
