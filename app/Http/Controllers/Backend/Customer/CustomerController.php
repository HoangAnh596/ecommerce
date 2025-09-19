<?php

namespace App\Http\Controllers\Backend\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Services\Interfaces\CustomerServiceInterface as CustomerService;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface as CustomerRepository;
use App\Repositories\Interfaces\CustomerCatalogueRepositoryInterface as CustomerCatalogueRepository;
use App\Repositories\Interfaces\SourceRepositoryInterface as SourceRepository;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerService;
    protected $customerRepository;
    protected $customerCatalogueRepository;
    protected $sourceRepository;
    protected $provinceRepository;

    public function __construct(
        CustomerService $customerService,
        ProvinceRepository $provinceRepository,
        CustomerRepository $customerRepository,
        CustomerCatalogueRepository $customerCatalogueRepository,
        SourceRepository $sourceRepository,
    ){
        $this->customerService = $customerService;
        $this->provinceRepository = $provinceRepository;
        $this->customerRepository = $customerRepository;
        $this->customerCatalogueRepository = $customerCatalogueRepository;
        $this->sourceRepository = $sourceRepository;
    }
    
    public function index(Request $request)
    {
        $this->authorize('modules', 'customer.index');
        $customers = $this->customerService->paginate($request);
        $customerCatalogues = $this->customerCatalogueRepository->all();

        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'Customer'
        ];
        $template = 'backend.customer.customer.index';
        $config['seo']  = __('messages.customer');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'customers',
            'customerCatalogues'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'customer.create');
        $provinces = $this->provinceRepository->all();
        $customerCatalogues = $this->customerCatalogueRepository->all();
        $sources = $this->sourceRepository->all();

        $config = $this->configData();
        $template = 'backend.customer.customer.store';
        $config['seo']  = __('messages.customer');
        $config['method'] = 'create';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'customerCatalogues',
            'sources',
            'provinces'
        ));
    }

    public function store(StoreCustomerRequest $request){
        if($this->customerService->create($request)){

            return redirect()->route('customer.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('customer.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id) {
        $this->authorize('modules', 'customer.update');
        $customer = $this->customerRepository->findById($id);
        $customerCatalogues = $this->customerCatalogueRepository->all();
        $sources = $this->sourceRepository->all();
        $provinces = $this->provinceRepository->all();
        
        $config = $this->configData();
        $template = 'backend.customer.customer.store';
        $config['seo']  = __('messages.customer');
        $config['method'] = 'edit';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'provinces',
            'customerCatalogues',
            'sources',
            'customer'
        ));
    }

    public function update(UpdateCustomerRequest $request, $id) {
        if($this->customerService->update($request, $id)){

            return redirect()->route('customer.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('customer.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id) {
        $this->authorize('modules', 'customer.destroy');
        $customer = $this->customerRepository->findById($id);
        $template = 'backend.customer.customer.delete';
        $config['seo']  = __('messages.customer');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'customer'
        ));
    }

    public function destroy($id) {
        if($this->customerService->destroy($id)){

            return redirect()->route('customer.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('customer.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData() {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js'
            ],
        ];
    }
}
