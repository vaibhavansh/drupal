<?php
namespace Drupal\scholarship_beneficiary_profile\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for multi-step forms.
 */
abstract class MultiStepFormBase extends FormBase {

  protected $tempStore;

  public function __construct(PrivateTempStoreFactory $temp_store_factory) {
    $this->tempStore = $temp_store_factory->get('scholarship_beneficiary_profile');
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('tempstore.private'));
  }

  protected function buildProgressBar($current_step, $total_steps) {
    $steps = [];
    for ($i = 1; $i <= $total_steps; $i++) {
        $steps[] = [
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#value' => $this->t('Step @step', ['@step' => $i]),
            '#attributes' => [
                'class' => [
                    'progress-step',
                    $i <= $current_step ? 'completed' : '',
                    $i == $current_step ? 'current' : '',
                ],
            ],
        ];
    }

    return [
        'progress' => [
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#attributes' => ['class' => ['progress-bar-container']],
            'steps' => $steps,
        ],
    ];
}


  protected function saveData(array $data) {
    foreach ($data as $key => $value) {
      $this->tempStore->set($key, $value);
    }
  }

  protected function getData($key, $default = NULL) {
    return $this->tempStore->get($key) ?? $default;
  }
}
