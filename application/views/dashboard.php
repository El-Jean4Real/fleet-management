<!-- dashboard.php -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><?= lang('dashboard_title') ?: 'Tableau de bord' ?></h1>
      </div>
      <div class="col-sm-6 text-right">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#"><?= lang('breadcrumb_home') ?: 'Accueil' ?></a></li>
          <li class="breadcrumb-item active"><?= lang('dashboard_title') ?: 'Tableau de bord' ?></li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Résumés -->
<div class="row">
  <?php
    $stats = [
      ['color'=>'info','icon'=>'truck','value'=>$dashboard['tot_vehicles'] ?? 0,'label'=>lang('total_vehicles') ?: 'Véhicules totaux'],
      ['color'=>'success','icon'=>'user-tie','value'=>$dashboard['tot_drivers'] ?? 0,'label'=>lang('total_drivers') ?: 'Chauffeurs totaux'],
      ['color'=>'warning','icon'=>'user','value'=>$dashboard['tot_customers'] ?? 0,'label'=>lang('total_customers') ?: 'Clients totaux'],
      ['color'=>'danger','icon'=>'road','value'=>$dashboard['tot_today_trips'] ?? 0,'label'=>lang('today_trips') ?: 'Trajets du jour']
    ];
    foreach ($stats as $stat): ?>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-<?= $stat['color'] ?>">
          <div class="inner">
            <h3><?= $stat['value'] ?></h3>
            <p><?= $stat['label'] ?></p>
          </div>
          <div class="icon"><i class="fas fa-<?= $stat['icon'] ?>"></i></div>
        </div>
      </div>
  <?php endforeach; ?>
</div>

<!-- Graphique Objectifs Hebdo -->
<div class="card mt-3">
  <div class="card-header bg-gradient-primary text-white">
    <?= lang('weekly_goals_title') ?: 'Objectifs hebdomadaires' ?>
  </div>
  <div class="card-body">
    <canvas id="objectifChartHebdo" height="60"></canvas>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById("objectifChartHebdo").getContext("2d");
  new Chart(ctx, {
    type: "bar",
    data: {
      labels: <?= $graph_labels ?: '[]' ?>,
      datasets: [
        { label: "<?= lang('goal_label') ?: 'Objectif' ?>", backgroundColor: "#00c0ef", data: <?= $graph_objectif ?: '[]' ?> },
        { label: "<?= lang('achieved_label') ?: 'Réalisé' ?>", backgroundColor: "#00a65a", data: <?= $graph_realise ?: '[]' ?> }
      ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });
});
</script>

<style>
  .badge-progress {
    display: inline-block;
    width: 110px;
    height: 16px;
    background: #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    vertical-align: middle;
    margin-left: 8px;
    box-shadow: inset 0 -1px 0 rgba(0,0,0,0.05);
  }
  .badge-progress-bar { height: 100%; display:block; }
  .badge-green { background-color: #28a745; }
  .badge-yellow { background-color: #ffc107; }
  .badge-red { background-color: #dc3545; }
  .text-right { text-align: right; }
  .text-center { text-align: center; }
</style>

<section class="content">
  <div class="container-fluid">
    <div class="row">

      <!-- Objectifs Hebdomadaires -->
      <div class="col-md-6">
        <div class="card card-outline card-primary">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">
              <?= lang('weekly_goals_per_vehicle') ?: 'Objectifs hebdomadaires par véhicule' ?>
              <small>(<?= date('d/m/Y', strtotime($week_period['start'])) ?> → <?= date('d/m/Y', strtotime($week_period['end'])) ?>)</small>
            </h3>
            <div>
              <a href="?week_offset=<?= $week_period['offset'] - 1 ?>&month_offset=<?= $month_period['offset'] ?>" class="btn btn-sm btn-outline-secondary">⟵</a>
              <a href="?week_offset=<?= $week_period['offset'] + 1 ?>&month_offset=<?= $month_period['offset'] ?>" class="btn btn-sm btn-outline-secondary">⟶</a>
            </div>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped text-nowrap">
              <thead>
                <tr>
                  <th><?= lang('vehicle') ?: 'Véhicule' ?></th>
                  <th class="text-right"><?= (lang('goal_label') ?: 'Objectif') ?> <small>(XAF)</small></th>
                  <th class="text-right"><?= (lang('achieved_label') ?: 'Réalisé') ?> <small>(XAF)</small></th>
                  <th class="text-right"><?= (lang('expenses_label') ?: 'Dépenses') ?> <small>(XAF)</small></th>
                  <th class="text-center"><?= lang('completion_rate') ?: 'Taux d\'achèvement' ?></th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($objectifs_hebdo)): ?>
                  <?php foreach ($objectifs_hebdo as $item):
                    $rate = (float) ($item['taux_atteinte'] ?? 0);
                    $barClass = ($rate >= 80) ? 'badge-green' : (($rate >= 50) ? 'badge-yellow' : 'badge-red');
                  ?>
                    <tr>
                      <td><?= htmlspecialchars($item['vehicule_nom']) ?></td>
                      <td class="text-right"><?= number_format((float)$item['montant_objectif'], 2, ',', ' ') ?></td>
                      <td class="text-right"><?= number_format(((float)$item['montant_realise'] + (float)$item['montant_manuel']), 2, ',', ' ') ?></td>
                      <td class="text-right"><?= number_format((float)$item['depenses'], 2, ',', ' ') ?></td>
                      <td class="text-center">
                        <span><?= number_format($rate, 2) ?>%</span>
                        <span class="badge-progress" title="<?= number_format($rate, 2) ?>%">
                          <span class="badge-progress-bar <?= $barClass ?>" style="width: <?= min(100, max(0, $rate)) ?>%"></span>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="5" class="text-center"><?= lang('no_data') ?: 'Aucune donnée disponible' ?></td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Objectifs Mensuels -->
      <div class="col-md-6">
        <div class="card card-outline card-success">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">
              <?= lang('monthly_goals_per_vehicle') ?: 'Objectifs mensuels par véhicule' ?>
              <small>(<?= ucfirst(strftime('%B %Y', strtotime($month_period['start']))) ?>)</small>
            </h3>
            <div>
              <a href="?week_offset=<?= $week_period['offset'] ?>&month_offset=<?= $month_period['offset'] - 1 ?>" class="btn btn-sm btn-outline-secondary">⟵</a>
              <a href="?week_offset=<?= $week_period['offset'] ?>&month_offset=<?= $month_period['offset'] + 1 ?>" class="btn btn-sm btn-outline-secondary">⟶</a>
            </div>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped text-nowrap">
              <thead>
                <tr>
                  <th><?= lang('vehicle') ?: 'Véhicule' ?></th>
                  <th class="text-right"><?= (lang('goal_label') ?: 'Objectif') ?> <small>(XAF)</small></th>
                  <th class="text-right"><?= (lang('achieved_label') ?: 'Réalisé') ?> <small>(XAF)</small></th>
                  <th class="text-right"><?= (lang('expenses_label') ?: 'Dépenses') ?> <small>(XAF)</small></th>
                  <th class="text-center"><?= lang('completion_rate') ?: 'Taux d\'achèvement' ?></th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($objectifs_mensuel)): ?>
                  <?php foreach ($objectifs_mensuel as $item):
                    $rate = (float) ($item['taux_atteinte'] ?? 0);
                    $barClass = ($rate >= 80) ? 'badge-green' : (($rate >= 50) ? 'badge-yellow' : 'badge-red');
                  ?>
                    <tr>
                      <td><?= htmlspecialchars($item['vehicule_nom']) ?></td>
                      <td class="text-right"><?= number_format((float)$item['montant_objectif'], 2, ',', ' ') ?></td>
                      <td class="text-right"><?= number_format(((float)$item['montant_realise'] + (float)$item['montant_manuel']), 2, ',', ' ') ?></td>
                      <td class="text-right"><?= number_format((float)$item['depenses'], 2, ',', ' ') ?></td>
                      <td class="text-center">
                        <span><?= number_format($rate, 2) ?>%</span>
                        <span class="badge-progress" title="<?= number_format($rate, 2) ?>%">
                          <span class="badge-progress-bar <?= $barClass ?>" style="width: <?= min(100, max(0, $rate)) ?>%"></span>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="5" class="text-center"><?= lang('no_data') ?: 'Aucune donnée disponible' ?></td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
