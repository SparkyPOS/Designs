<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPrintArea;
use Illuminate\Http\Request;

class ProductPrintAreaService
{
    private const DEFAULT_IMAGE = 'default-product-print-area.png';

    private const DEFAULT_SELECTION = [
        'type' => 'rect',
        'left' => 156,
        'top' => 90,
        'angle' => 0,
        'width' => 285,
        'height' => 440,
    ];

    public function ensureDefaults(Product $product): void
    {
        foreach (['front' => 'Front', 'back' => 'Back'] as $side => $name) {
            if (!$this->findSide($product, $side)) {
                $printArea = new ProductPrintArea();
                $printArea->product_id = $product->id;
                $printArea->name = $name;
                $printArea->image = self::DEFAULT_IMAGE;
                $printArea->selected_area = json_encode(self::DEFAULT_SELECTION);
                $printArea->save();
            }
        }

        $product->unsetRelation('productPrintAreas');
    }

    public function updateFromRequest(Request $request, Product $product): void
    {
        $this->ensureDefaults($product);

        foreach (['front', 'back'] as $side) {
            $printArea = $this->findSide($product, $side);
            if (!$printArea) {
                continue;
            }

            $selectedArea = $request->input("print_area_selected.$side");
            if (is_string($selectedArea)) {
                $printArea->selected_area = json_encode($this->normalizeSelection($selectedArea, $printArea));
            }

            if ($request->hasFile("print_area_image.$side")) {
                $oldImage = $printArea->image === self::DEFAULT_IMAGE ? null : $printArea->image;
                $printArea->image = fileUploader(
                    $request->file("print_area_image.$side"),
                    getFilePath('printArea'),
                    null,
                    $oldImage
                );
            }

            $printArea->save();
        }

        $product->unsetRelation('productPrintAreas');
    }

    private function findSide(Product $product, string $side): ?ProductPrintArea
    {
        $areas = $product->productPrintAreas()->get();

        if ($side === 'back') {
            return $areas->first(fn (ProductPrintArea $area) => str_contains(strtolower($area->name), 'back'));
        }

        return $areas->first(fn (ProductPrintArea $area) => !str_contains(strtolower($area->name), 'back'));
    }

    private function normalizeSelection(string $value, ProductPrintArea $printArea): array
    {
        $decoded = json_decode($value, true);
        $current = json_decode($printArea->selected_area, true) ?: self::DEFAULT_SELECTION;

        if (!is_array($decoded)) {
            return $current;
        }

        foreach (['left', 'top', 'width', 'height'] as $key) {
            if (!isset($decoded[$key]) || !is_numeric($decoded[$key])) {
                return $current;
            }
        }

        if ((float) $decoded['width'] <= 0 || (float) $decoded['height'] <= 0) {
            return $current;
        }

        return [
            'type' => in_array($decoded['type'] ?? 'rect', ['rect', 'circle'], true) ? $decoded['type'] : 'rect',
            'left' => round((float) $decoded['left'], 4),
            'top' => round((float) $decoded['top'], 4),
            'angle' => 0,
            'width' => round((float) $decoded['width'], 4),
            'height' => round((float) $decoded['height'], 4),
        ];
    }
}
