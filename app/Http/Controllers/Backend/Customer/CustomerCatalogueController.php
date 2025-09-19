<?php

namespace App\Http\Controllers\Backend\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerCatalogueRequest;
use App\Services\Interfaces\CustomerCatalogueServiceInterface as CustomerCatalogueService;
use App\Repositories\Interfaces\CustomerCatalogueRepositoryInterface as CustomerCatalogueRepository;
use Illuminate\Http\Request;

class CustomerCatalogueController extends Controller
{
    protected $customerCatalogueService;
    protected $customerCatalogueRepository;
    protected $provinceRepository;

    public function __construct(
        CustomerCatalogueService $customerCatalogueService,
        CustomerCatalogueRepository $customerCatalogueRepository
    ) {
        $this->customerCatalogueService = $customerCatalogueService;
        $this->customerCatalogueRepository = $customerCatalogueRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'customer.catalogue.index');
        $customerCatalogues = $this->customerCatalogueService->paginate($request);
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'CustomerCatalogue'
        ];
        $template = 'backend.customer.catalogue.index';
        $config['seo']  = __('messages.customerCatalogue');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'customerCatalogues'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'customer.catalogue.create');
        $template = 'backend.customer.catalogue.store';
        $config['seo']  = __('messages.customerCatalogue');
        $config['method'] = 'create';

        return view('backend.dashboard.layout', compact(
            'template',
            'config'
        ));
    }

    public function store(StoreCustomerCatalogueRequest $request)
    {
        if ($this->customerCatalogueService->create($request)) {

            return redirect()->route('customer.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('customer.catalogue.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'customer.catalogue.update');
        $customerCatalogue = $this->customerCatalogueRepository->findById($id);
        $template = 'backend.customer.catalogue.store';
        $config['seo']  = __('messages.customerCatalogue');
        $config['method'] = 'edit';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'customerCatalogue'
        ));
    }

    public function update(StoreCustomerCatalogueRequest $request, $id)
    {
        if ($this->customerCatalogueService->update($request, $id)) {

            return redirect()->route('customer.catalogue.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('customer.catalogue.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'customer.catalogue.destroy');
        $customerCatalogue = $this->customerCatalogueRepository->findById($id);
        $template = 'backend.customer.catalogue.delete';
        $config['seo']  = __('messages.customerCatalogue');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'customerCatalogue'
        ));
    }

    public function destroy($id)
    {
        if ($this->customerCatalogueService->destroy($id)) {

            return redirect()->route('customer.catalogue.index')->with('success', 'Xóa bản ghi thành công');
        }

        return redirect()->route('customer.catalogue.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
    }
}
