<?php

namespace Propeller\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

use Exception;
use Propeller\Includes\Controller\PageController;
use Propeller\PropellerSitemap;
use Propeller\PropellerUtils;

class PropellerAdmin
{
    protected $propeller;
    protected $version;
    protected $plugin_screen_hook_suffix;

    const PROPELLER_LOGO = "data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDU0IDY0IiB3aWR0aD0iNTQiIGhlaWdodD0iNjQiPgoJPHRpdGxlPmxvZ28taGloaS1yZXM8L3RpdGxlPgoJPGRlZnM+CgkJPGltYWdlICB3aWR0aD0iMzM2IiBoZWlnaHQ9Ijc3IiBpZD0iaW1nMSIgaHJlZj0iZGF0YTppbWFnZS9wbmc7YmFzZTY0LGlWQk9SdzBLR2dvQUFBQU5TVWhFVWdBQUFWQUFBQUJOQ0FNQUFBREhDY3dhQUFBQUFYTlNSMElCMmNrc2Z3QUFBdmRRVEZSRnA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRwNnF0cDZxdHA2cXRMeGY3WXdBQUFQMTBVazVUQUI0dUlnd0dwdnEzUXFuL3UwTWpDUnhYb3R6OGhoRnM1ZjdxTkViUC9YVURVK3pnV1V3QkZUVjhqcFdSaEc1TktoSUlKVTkyalFzbVJHcURsN1V3V0htTGxKS0hjVkVGSUQ5Z2ZwT0pXend2Vm9oL1owb1BCRWgwT3hjQ004UDJjMVJKbnVFdGFhMUhjTHIwOGIyQ09oaWQyWnRjSHczcmVESUhOOTFtdFBYSVk3eWt3dk9XRytmTnJ1Nzc0NmR0MC9uNHg2eTI1Q3pmc2ZKZjVoRFVXdmNLbk11WTZjVFYyaHFyRnFPL0o4eXdQZWp2UmJPUXNxVXIwS0dxYTg0VTJONTlPWUVUaGNtNWdKcnRRY3BsMWhrT2Nvd3g4RlZkdUo4a1B0c29ZcjVvUzljaFpFNUFPQjNBcURZcFh1TFN4dEhGajVuQmQ2OXZlNkI2WVlMaEdCWUFBQkcyU1VSQlZIaWM3WnQ1UUJSSDFzQzdGWlF4QzVoRkJWRU14RGlLSnhvRnhSakpZVUFTbFNnR0FpT0tSdU9CRWhHSllGQ09GVHlEcmtGUlVjRk5BZ1pFRk1Yd0tZR2c0aEV4RVFsWjhZTDFpSkxnc2RHb0t3azdmVlc5NnE0Wm1tOW4vYjUxNS8zUi9icnUvazExMWF0WE5TenpYeUFzaTlYZitKdkY3emkyOVdPVDFtWEt3djYvU2hzQTlDRi8wd0NnamIrWnNxNy9DcUR0QU5CNy9NMGE5dEMvbTdJdU0xQXowSmFMR2FpSnhRelV4R0lHYW1JeEF6V3htSUdhV0o1aW9MYnN2Y1luV3lNblR5blE5cXhlSG1qWW41NWNsYUk4bFVBNzZXbHlMYmRscnoycEtwRThqVUM3c213RHIzUmdhNTlRbFZpZVFxQXU3RTFCY1dpNDgyUnFoUEwwQVgzaFIxRnA3SGIraVZSSXl2OHhVQzNMTmxxeW9ueXJxcFJCWE5MYkZGWWVYTVRkOWhkK2tSSWVWK0Y4N05STHF2M1FnUHMxelNiMzVsTCtkTitGdmRHWmZkUlVSRW5SY3FCYTFwVTkvZUkvZnIzaHlyTG50VjgwMzJhOUJPUk81Sm9RWEk5Q0xseldYM1RzbFc1c3JoaGlYKzkvc0Y2QklQUXFVcDIyNlMrV2czdXpXUXpqZGxSZWd3M0w3bW5UQUVMc2ZOUGtoVm40M1pWVXpWNHVVOTFBbHMyUWF0S2p5cmdyendKRjYzdTNQWnNyMWFFSjBXZjQ4Vk41b3BZQ2pYclVkTWRtdS9RVXR1NER0dWkwUEUxNE5WSnJ1WjRVdys3eDI5SEFSTENUOGxERTVBMk0xblVBdTVySTZPL0NmaXg3cFlRVlNGMFV5ekJCVjM1NHdPa3lvTGJqdTdOSnNsWXN6bEIwdUdRMlVWS1hiSzFoZWsxbDQ0ajQrSHMzdHhnMlhWLzJhYnVhNUdIRFJ1WWNrNlZxR2RDb3ppd2JJMjlrOWVrVFpOQzhyVWhkRWNZd1BtUHFlVzR5b0lOY1hsdW9hSFR5USt0WlJBQUJWS3VadFVEUVNhQzk1clB6NVNXbFJDbG5KQUxvd05mWmNFV0twQXNOclJTZGpoZEx0MXUvVXI3WHNNSVBwaEVCTFFKcS9iakpRbGxta2xYMU9pS0FCTHE0KzF4Qko0SG1MbzJpTmp6UytjdWQ0SkVBT25Qb0hGR0hRRE8vektPMGF0cWZ1YXVPb0FPQjN1MFY4WUJXZitQN2Y2WUZ0MThSUVcwdXc2U1ZiWUdQTFFCcXVjQXhtbDVtMHVGYytFZ0FEWHBKNm9jRVVEWjBtNEVHMnF6U2dTY0k5R0h2R1pJT2dHWmFUcWVVa2g1ZHE3LzJpbU1EUUNBQW10NDYxRUQ5VE5OajVXUzJzU0xMVUhMR3Z1Y0I4S1FlcUhabXJNRXltY1hWb0N0QW9QWXpIMG82QVZSRDdSNkNaRTdEQXlrQW12aGNDTkl4MER3VzhwY2syWnJqNVJFZHZLd0l2QzBBYXNNWU5tQVNuU2FTQWRwVjdMdUdtOHRrczIvaEI5VkFvMGErWTZSTUptY2MvbGtCMEZ4MkF0SUpvRVpsMmszMDFRT2dWbTF3YXhEUUl2WnRXZ241MXlmcnI1NkIwWXgxYTd6OEJFQ05pZDFvTzNLR0MrKzJ4R2lHVlhmeHA2c1dhTXlyNDR5VzJkaXpQK3FqQUdpQ0phNUtQZENZb2EraEFsWlFVMGhBUHhnN2hoYWR2VFZmZnkxaDM5UmYxMVNnTVU0bFVDWSs4NWRhOEdpeGVuRXpHWmJ2UXlhcFNxRGFmbDgyVTJib0tFUWNBSVdpSGloVDZOVWthc2FCMnU2YzBVQ0wzclZkMzhXMTZUbytjc0hGejhSZ3RVQ1owSUFSNE9tSU54R3BpYlN5WDN5VENMSVpzbGRTVlFLZDZrY09JaW5hNzZ3K0lvZkJCZDdEUmExRlFCTnFmb3E4MVAzb1VqSzBlSVE0Z2hnSG12SVJHUnpUM3NWdVVYaHI2LzJyOUE5K1hUS0UwQkozTVpvR3RMVGt3RDlZSHl0Wi9WdUNzSDVxSkl3cGk3RFhMMmEwaVQzWWwwQ28vM3czVVZNSDlFd0o3UFF4bTIrUDFYL2Z0cjgvY29EOTQxaC9VVkVQZE0xdzlxMWFUc25aM21FbnRIMGEzODRXRkJwUXV3UEhpdmxKTjdrUC9KblgxVy9vdzM5NXVnNXJ1VnZpY3FtdzBmbUNvZ0JxbjFBb21KMFdsU1dOaElGOXlsWFNMS3BlQkJtQ2wwdHpSV2FKSnpCK3YvSVFGVlZBdFM5OGkrZUQrREdzVmxTMSs0L1B4SW5QYkJPSGNocFFtME1GNVFxZ1pibkpTTGZ3U3ZZQ1VidVdDcXNGQlZETlIzWDNXeFY0OFYveHlWZHdlSkpIVmdOaGRQWmFKQm10ekJjN2hFbE9EclFza0VXcktZc3VnWitBcUdWcmEwWHQyVWc4UTVYT09nWFNqUExDTVFXenpnbUtLcURCclhmaEYvcTRhaVZPY1BIdm9PTi8wMXU0SzRCcW1wcGVkQ3JmS2dkNmVpc29pWEYyMWMzQVQ2a2RCRk5FRHJUTTZ3RmFITm9rNGs2Vit2SkEyYUkxQkxlWmNSUjhLVEtnMnp1K0FoK0QzT0ZuK0xKb2JubW45VVZoamQ1N1lZYUEwcmJvQzIwODV5d29xb0RHcCtFT3VqU2JXTHN2M0lXSDU0Vnh3bDBPdEN6aUI5NHJJZ002eUpkY0orZ3FSMlRncHo0bitac01hT1pVWUQ3MnU0UlVqWHQxTFZrcFUxY1ppUFRIem54UEpJSHViM3FGekRFc0VIejFNMUtFdTAxc0hBcUxqaU5YK2lYZDBjREFiQmV0U2xWQU4rRmwxNnByeThoV2dDa3dhWm5nVFpJQlhlN25KRlpBQUQwWi9CMGpreDFnSFg5a0lIOGpnUmFIQTdkQjVsWDh4ZVg0eXN0aW1LdWVxUDlZaC9KakN3RTBlVUlYZVk3bmZzWjYwdWRDVlpYRFVGQjhrS01zdzJsc0RKeUpFUnh2YW9CbWZvVlhsczdmeThxMHpNTWRvZm81L2tZQzNWMHF2VGNKZEpweXpleHpCdlMvcnZ5Z1JBQmRrM0lPUEYzVEluWEhIMldkalcvWlhuK2tlKzNucmdUUVRhR0tGYWJsTGpESjllZjlTTjV4cjZPUTl5MVd5akw0MUtEdjAvK00wRC9VQUkzY2lFT2V4ZjVKcVJvOEZSUUpBeW9CdExScloxUUJCQnE3bkxJL01YOHoxc2Z3OHp3RTJ0ai9HNWpZSGYyMlNhc1VyZUlrTHhLOTdvYzFYQ3NoME9TTDY1UTUzRUYzbWNzYkNUZUs4Y0N1ZE9kazlzZFRTSTNRNGRVQWJXT0pBdXlXa3M0cWh2am1kWnY0R3dIMFFFZ3RxZ0FDelEybitNZjNBUE52U0NsM2hVRFhGMzhHMHVyV1BDK3BTeHdtSzh2U0d5RkhYQ1IxVFQvT1NJWkFqd3loZUQ0MXoyRGJ1dTB0N3ZyVGk5Z3lIRlNseU5BS2VTcVlpUm44VFEzUWJYTlJ3SlRQRldXT3owWnFRU1EvWVVHZ29YRWRjQVVRYUcraXQ0bGltNENuQldHVWgwQTl2b0pwRjI1REx6T2duRklXVjBTcE5GWk5pZWpPa0VCZjJVZkpjQ01iVC9RVGpuTy8rTWhUbEdSVUVZZHhGVUIxclFubm5CRXA3TXdQYXhEb2hDdzhVaEZBYXhSVEFpY2ZwU0IxeVlmY0ZRTEZ4alluNDc2UjdLUklCOUlwamNTbVFKcXJiQ3E0K1FRQ2ZXTTNKVU5tT1I2L2xwVnpzOHdvQTcrVlVuYU40ZDlVQmRCTzk5V1dtVFdFSHk4aDBHNC9ZSjBBR2tTNFpTVzVqSTArMit2Y0ZRTE5JOWJVMVN1azM5a2huVElsY2FJTlFRdUhLRzV0Q1lDaTFSTXB2ZXVRT2orWk93OWZQb3BldEZKV2ZzMVA4eXFBRGoycnRzelFtem5jRFFEVi9OQVJSeE5BWjYraWxRQU1OUDlNN2dxQTJuUWp0bG9hdGt2cjd5T1YxQ0ZVTHl2akpjM3pJRU1BalhFZFQ4c0Ftdjd1S1c3YUxweElTMGFUU3hkNFg0WUtvQzlYcUMzVHYreXlyRlZ4aThEZkhnaWdGeDFvSmVRSEl6V0ozL0VCUUtla0VrbDlUMG9UeVBwUEZQdUVvaHhDSGpCK3ltZ2U2SWxYa1hwbEp6ZVF2SGJjUU5FS21YQ0N0K2xVQUZYdGRHTlM1OS9tYnRBZmVocE16QVRReXU2MEVnQy9rMzFrQWF0bUUwbGZtQ3ROWUpKTnJaVGp5Sy9LejFzQWFMSWxkZHc5T3hTcGF6STRsdTV5dTl1Z1JBL25CeDRWUUtjclozWURrdXJPcitZQlVLdVZ3TXdpZ0w2K2gxYkM3YTVJWGNQN1hRRFFRc0tOeGt5MGwzYXpiWDlzWXFoaVVZamMrWjI0YndkdWdaVDFvR1R3L3VZUjB0LzM0TGFqWkw1UUk4SzI0OWVKS29CZTZhVzJ6SmdwL05RTmdPNS9IVmg3Qk5DMXRLMDFuVzBHMHFONVh5ZmNBaUY5eVg1dlNMdC9kbm1ENmUwSmQ1ZXNjczEwYm5xQ3N6dy9xTW9sT0JYYkhuL3N5QTBrOXZkUVFIYThBV05Da00yM2VjTmFCVkNMdGppZ2FiMnhNb3RZM2sybUN1Z20ydDVhTkZpT0NsYW5ZYUNaTnFpRUZhdHJxZTBaanpZbXNrSzRkUmtFV3JWb3B6TEQ0WXZJNWNjc1NPQ3VkYjFSUVB6OXBjb2NDbEZqMkR0aXgxaTB6RVZPRlZWQWx5UlJqcjI0L2czcm8zbXJ5REJRc0lBTG02WmxLQUsrK04yTHVPNEdnV2E1VTJaRjZCMFJmdEJXYTdGTHpFVzVVbEtLR3FCMzhIZGdQNFR5dThwRkZkRGs0OHFoT1NvWGJOVmM0RzFhSTBDTHh5TFZyU3V0V2ZIcjBVSXlrRzhTQkdwVFphZklvRTBEVzM3Zk8zUFhrdmR1b0pEcitZYk1NeUJxZ1BhOWpFUG9jd2twcW9BeVZZR3lFenlFcTR4Si9veVBOZ0owYUIzeVRXWEhVMWFJUCtlam96YVJwVi96WlVKdlU5b29SUmZ0K2h2d2RvMy9DM2ZWUFlQWFRvM3RiaXRyQ2ZqdUhQR3NCbWdRZ0ppZlR1a0xINndsSHRVQlRTcjlYdVllOFh0K0UzNG9FM1p5akFEVmJVQitMR2I5ZXNWWlNHOW5qR0wzWFA2dENhQjJmbkwvNGRGejJHbkIyTGNYak51M0szQzE2U2ZXTW5JNVlqSGxyL0JaRFZEdmNmaFVWK2s3b0x1S2NyaGZ4U3pJUmgxUTVxUWZlY2l6NURKNEgybk5hZ1FvbzJtRjlhMm5aYjdLbUt0Z0MwVDB4SkFlZXp2UGJDSkhWSGQ0ZkVuYStSKzRGdml1YzBmTG1zRE0yc0ZFMWgrb3hRR3FQUGEvZ0kvRDlrMzVJcHo3dEQ1OGJUZ09VQW1Vc1krRjU4SW05WUMrNUxCSndxcmVHRkJkS3ZhZ3owZ2hvZ0phandiYmgyRlhoZVdGYkU4cHRhNFFqenJhZld0M2d3OWVzMWxhU2ZWdEFNRUxFZ21UZDZkTHRkNHNzRHVZZ0pjdnFvQWV2b085OGt4czFwdndvSXJsalJ6dWlPRjZIODlhS1VndFVDYlp6ZUV0c1d0M0NwazVBRVp0bkNzWUFjYUFNaTV6SkVNbTVnNTBEUVRrem5HRTZNYUtKNzNrdTU1VFZoemRKdjZrRnEvbUVQc2JhWk1rYmRJNkp4QmUwUE5MdEZUWnVqeCtnUUNvZmpqYXpsRUZOSk9GSm0ycDcrTk8waGNlY0s2cnYyQzZoVTA1S2MyQnFvSHFPMm1YSXI5TWk3R3BxWjIzRW9lM1VpNkxmaUtqUUpsemcwVGxvQ2QzclF6ZjRENXZTcFRUL0RTNEk4d1VIQko3Z1BLZ1F5Zzc4VSt1UmRkRzFIMWFUQmlaalRPeFAvL1hqakFtZnZEc2pvZDM1amkrRjN2K0RVZVVwL1RJWDBSL2dycUREbTVidkdDaHNZUFBsdTd0dUh0ZS9KRjJEbWl6dnpIZlYvd2NXZ0JVYjc2TTk2NXV2VUorRmkvd3FtaVRHd2ZxbGlCc0FxV3Y1aHc0WFVabkpYa1czM2FkUXlZS3Jma2ZVYU1leFluVVBoandrdnlBNldkUmVGSTQrdmhOTXRLLy84UkR6MWVmSUx6RU1Uc3JCVVVkVU9jWmFlVGJhTnFVbFdudS92SXgwYTIyeEFtdGFCRlFxaFRXUzE0ejQwQ1p1aVhjYXdsSGkzUmJPaWdUNkNNdnZ5ZE45NnJQTnNXTTZ3dWUvQTVRRHZUS3BMQmEvSWhWbm0zcWRMbFQ4ODI0SmE1Ui8yV2c2NHB5SkxVWm9Jei9hZjFhb09JRU45cTR6S2VkQjA1S2QwUUxVTFZBSStlUlJuOXhYa1l6T1ZJY3BTT2lhbzh6WHJrbzYvY0tzWnNnVGJUL0tsRE5ZbnhvcURtZ3VzcUlPYUdMT1lOMFlLbDgxNXlUNUxvUzdDeFZDVFRwL3Rma0gyWnNDMFlaNzZQSlZXaTdWdldCMjJoYjQ0MnhpYnNsZFJCVlFMTU1uZ25XUEsvRkpubHpRQmx0L3U2RlUvWHB0UnQxbERQSm1tdkRnUE1aQXIyaU5YaUcydVdLM00zZ2ZXdk9IR3BTUVNKL3grV3FQeEplWkVVNW5ZSEU3cm5seUJKVkJYUkl1UU1WRUpNZE5oUllwODBDWlpqd09aeHJ4Ty9zRFdWVTdQVFpjR0VIZ1U3N2FnYjlId1B4Kzc1VnVtMmM3NFp1b3FRVjJ4Z0lOaHhiOEtlRnZCOVhHSGdsZmY4YzdJcFhLcXFBUnZnTXlLWDk2b1hMQmtNelZ3VlFRZnJVS2hzMWZSTHBSWVpBNzFUMzJFQnp4MW5YVHFZNVdyUzlCOUZQcWpMeGwyeXZnR04vTGZsYlRVK25TK1RCWFNSWnZuYmdWMVVITkpIcGtnalhtcnhvT2w0bnU0ZGFvSWZmVm56QlpRbldzcjhjRVVBdG1hSmppbzNDcEttVncrVmhrbHcvRmtJSi9iVDBBT0dTYU5rZnYzbzYrVkIrMVZJM0QyS1hUQzFReHR1OXdTVU9CNTJzU1Q4cVA1OXpFRk04YXNTNWJma3pPU09sTGloUHVhM29hVEtnektCV2liZkFFVXIva2Rvd3hRRTJJQUZCNjZiL0ZmNzNMeW1oUExpWDdFZnJpejNHNDl2d3QzRGdRM3RYc1ZsVHN6Uzlod1hzS3BzditqWEl0c1ZuNGMwZjUzUEFoYWdBcW4rbHRoZml4dXo3azFWaWllZUQzQTQraW5NK1RNK3ZrWHJEVFJHTEJCMGNzOXRWTS9yZWI1T0doanRSVXNtQjZ0dm4rYWgrZG5HaDFjT3FvdW5Id3VJTTdPOWpLWms2TUtSZG1mUDVuTVRSOTNhVUZMWHRyamhQZEJSdlBvNFVISHVEQ25Gc09HVTRzZlIxSEhmY3EyS04xY1RNeCtuRC8vQmVqdUlRMnp2WUw3YThFTlJJQWNxSng3QjEzaGtqbnpHMEZheEcya2NuaUZyVlk1dkRxeFdPVmxHVVFEbXhkYWp4THZyaVZ4WE9ZMEVzbjExLzZteUc0VnIrTjZMTER2amN1MTl3UldKdFMzSVpBR29DNlRkWkhJa0tEaVliU1VZSCtwOHIvemFnN0I3UnFFMXJFMkFzblJtb09ySDluRCtvWnhleC8zeXQwWVJtb09wazJMdDZFejFteUw2MjFPTlNRTXhBMWNuR1QrYUZCbDFvMi93a1lRWnFZakVETmJHWWdacFl6RUJOTEdhZ0poWXpVQk9MR2FpSnhRelV4UExVQVMwQi85d0twKzc1L252Rlk0UUc2UUY5alNUOEQ1Ri9Bc25NbFBjYWQ5QWRBQUFBQUVsRlRrU3VRbUNDIi8+Cgk8L2RlZnM+Cgk8c3R5bGU+Cgk8L3N0eWxlPgoJPGcgaWQ9IldISVRFIj4KCQk8dXNlIGlkPSJWZWN0b3IgU21hcnQgT2JqZWN0IGNvcHkiIGhyZWY9IiNpbWcxIiB4PSItNzQiIHk9Ii03Ii8+Cgk8L2c+Cjwvc3ZnPg==";
    const PROPELLER_MENU_POSITION = 2;

    public function __construct($propeller, $version)
    {
        $this->propeller = $propeller;
        $this->version = $version;

        $this->register_actions();
    }

    public function scripts()
    {
        // Bootstrap bundle includes Popper.js v2 internally
        wp_enqueue_script('propel_admin_bootstrap', PROPELLER_ASSETS_URL . '/js/lib/bootstrap.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script('propel_admin_validator', PROPELLER_ASSETS_URL . '/js/lib/jquery-validator/jquery.validate.js', array('jquery'), $this->version, true);
        wp_enqueue_script('propel_admin_validator_extras', PROPELLER_ASSETS_URL . '/js/lib/jquery-validator/additional-methods.js', array('jquery'), $this->version, true);
        wp_enqueue_script('propel_admin_loader', PROPELLER_ASSETS_URL . '/js/lib/plain-overlay.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script('propel_admin_accordion', PROPELLER_ASSETS_URL . '/js/lib/accordion.min.js', [], $this->version, true);


        // custom propeller admin js (depends on Bootstrap for tooltips)
        wp_enqueue_script('propel_admin_js', plugin_dir_url(__FILE__) . 'assets/js/propeller-admin.js', array('jquery', 'propel_admin_bootstrap'), $this->version, true);

        wp_enqueue_script('propel_admin_tagsinput_js', plugin_dir_url(__FILE__) . 'assets/js/use-bootstrap-tag.min.js', array('jquery', 'propel_admin_bootstrap'), $this->version, true);

        wp_localize_script(
            'propel_admin_js',
            'propeller_admin_ajax',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('propel-ajax-nonce')
            )
        );
    }

    public function styles()
    {
        wp_enqueue_style('propel_admin_bootstrap', PROPELLER_ASSETS_URL . '/css/lib/bootstrap.min.css', array(), $this->version, 'all');
        wp_enqueue_style('propel_admin_accordion', PROPELLER_ASSETS_URL . '/css/lib/accordion.min.css', array(), $this->version, 'all');

        // custom propeller admin css
        wp_enqueue_style('propel_admin_css', plugin_dir_url(__FILE__) . 'assets/css/propeller-admin.css', array(), $this->version, 'all');
        wp_enqueue_style('propel_admin_tagsinput_css', plugin_dir_url(__FILE__) . 'assets/css/use-bootstrap-tag.min.css', array(), $this->version, 'all');
    }

    public function menu()
    {
        // plugins_url( 'propeller-ecommerce-v2/admin/assets/img/propeller-wp-logo.svg' );

        add_menu_page('Propeller', 'Propeller', 'manage_options', 'propeller', array($this, 'general'), self::PROPELLER_LOGO, self::PROPELLER_MENU_POSITION);
        add_submenu_page("propeller", "General", "General", 'manage_options', 'propeller', array($this, 'general'));
        add_submenu_page("propeller", "Pages", "Pages", 'manage_options', 'propeller-pages', array($this, 'pages'));
        add_submenu_page("propeller", "Behavior", "Behavior", 'manage_options', 'propeller-behavior', array($this, 'behavior'));
        add_submenu_page("propeller", "Translations", "Translations", 'manage_options', 'propeller-translations', array($this, 'translations'));
        add_submenu_page("propeller", "Valuesets", "Valuesets", 'manage_options', 'propeller-valuesets', array($this, 'valuesets'));
        add_submenu_page("propeller", "Sitemap", "Sitemap", 'manage_options', 'propeller-sitemap', array($this, 'sitemap'));
    }

    public function general()
    {
        global $table_prefix, $wpdb;

        $settings_result = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i", $table_prefix . PROPELLER_SETTINGS_TABLE));

        require 'views/propeller-admin-alert.php';

        require 'views/tab/propeller-admin-general.php';
    }

    public function pages()
    {
        global $table_prefix, $wpdb;

        $pages_result = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i", $table_prefix . PROPELLER_PAGES_TABLE));

        require 'views/propeller-admin-alert.php';

        require 'views/tab/propeller-admin-pages.php';
    }

    public function behavior()
    {
        global $table_prefix, $wpdb;

        $behavior_result = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i", $table_prefix . PROPELLER_BEHAVIOR_TABLE));

        $sso_data = null;

        if ($behavior_result->use_sso) {
            $sso_data = $behavior_result->sso_data ? json_decode($behavior_result->sso_data) : null;
        }

        require 'views/propeller-admin-alert.php';

        require 'views/tab/propeller-admin-behavior.php';
    }

    public function translations()
    {
        $translator = new PropellerTranslations();

        require 'views/propeller-admin-alert.php';

        require 'views/tab/propeller-admin-translations.php';
    }

    public function valuesets()
    {
        $valuesetsController = new PropellerValuesets();

        require 'views/propeller-admin-alert.php';

        require 'views/tab/propeller-admin-valuesets.php';
    }

    public function sitemap()
    {
        $sitemap = new PropellerSitemap();

        $sitemap_files = $sitemap->get_files();
        $sitemap_valid = $sitemap->is_valid();

        $slug_langs = [
            'en',
            'nl',
            'it',
            'es'
        ];

        require 'views/propeller-admin-alert.php';

        require 'views/tab/propeller-admin-sitemap.php';
    }

    public function save_settings()
    {
        global $table_prefix, $wpdb;

        $success = true;
        $message = '';

        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'propel-ajax-nonce'))
            die(json_encode(['success' => false, 'message' => __('Security check failed', 'propeller-ecommerce-v2')]));

        if (current_user_can('manage_options')) {
            try {
                $data = $this->sanitize($_POST);

                // Special handling for API keys - don't overwrite with masked values
                if (isset($data['api_key']) && $data['api_key'] === str_repeat('•', 32)) {
                    $existing = $wpdb->get_row($wpdb->prepare("SELECT api_key, order_api_key FROM %i WHERE id = %d", $table_prefix . PROPELLER_SETTINGS_TABLE, $data['setting_id']));
                    if ($existing) {
                        $data['api_key'] = $existing->api_key;
                    }
                }

                if (isset($data['order_api_key']) && $data['order_api_key'] === str_repeat('•', 32)) {
                    if (!isset($existing)) {
                        $existing = $wpdb->get_row($wpdb->prepare("SELECT api_key, order_api_key FROM %i WHERE id = %d", $table_prefix . PROPELLER_SETTINGS_TABLE, $data['setting_id']));
                    }
                    if ($existing) {
                        $data['order_api_key'] = $existing->order_api_key;
                    }
                }

                $vals_arr = array(
                    'api_url' => $data['api_url'],
                    'api_key' => $data['api_key'],
                    'order_api_key' => $data['order_api_key'],
                    'anonymous_user' => $data['anonymous_user'],
                    'catalog_root' => $data['catalog_root'],
                    'site_id' => $data['site_id'],
                    'default_locale' => $data['default_locale'],
                    'cc_email' => $data['cc_email'],
                    'bcc_email' => $data['bcc_email'],
                    'currency' => $data['currency'],
                );

                if ($data['setting_id'] == '0')
                    $wpdb->insert($table_prefix . PROPELLER_SETTINGS_TABLE, $vals_arr);
                else
                    $wpdb->update(
                        $table_prefix . PROPELLER_SETTINGS_TABLE,
                        $vals_arr,
                        array(
                            'id' => $data['setting_id']
                        )
                    );

                // Destroy any caches in case the API key is changed
                $this->destroy_caches(false);

                // Propeller::register_pages();
                // Propeller::register_settings();
                // Propeller::register_behavior();

                // PageController::create_pages();

                $message = __('Settings saved', 'propeller-ecommerce-v2');
            } catch (Exception $ex) {
                $success = false;
                $message = $ex->getMessage();
            }
        } else {
            $success = false;
            $message = __('Not enought rights to modify plugin settings', 'propeller-ecommerce-v2');
        }

        die(json_encode(['success' => $success, 'message' => $message]));
    }

    public function save_pages()
    {
        global $table_prefix, $wpdb;

        $success = true;
        $message = '';

        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'propel-ajax-nonce'))
            die(json_encode(['success' => false, 'message' => __('Security check failed', 'propeller-ecommerce-v2')]));

        if (current_user_can('manage_options')) {
            try {
                $data = $this->sanitize($_POST);

                // delete any pages for deletion
                if (!empty($data['delete_pages'])) {
                    $del_pages = explode(',', $data['delete_pages']);

                    foreach ($del_pages as $p) {
                        if (!empty($p)) {
                            $page_result = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE id = %d", $table_prefix . PROPELLER_PAGES_TABLE, $p));

                            // delete the page from Wordpress
                            $wpdb->delete($table_prefix . 'posts', [
                                'post_name' => $page_result->page_slug,
                                'post_type' => 'page'
                            ]);

                            $wpdb->delete($table_prefix . PROPELLER_PAGES_TABLE, ['id' => $p]);

                            // $wpdb->delete($table_prefix . PROPELLER_SLUGS_TABLE, ['page_id' => $p]);
                        }
                    }
                }

                // insert new page definitions (or update)
                foreach ($data['page'] as $index => $page) {
                    $vals_arr = [];

                    $vals_arr['page_name']              = $page['page_name'];
                    $vals_arr['page_slug']              = $page['page_slug'];
                    $vals_arr['page_shortcode']         = $page['page_shortcode'];
                    $vals_arr['page_type']              = $page['page_type'];

                    $vals_arr['page_sluggable']         = isset($page['page_sluggable']) ? 1 : 0;
                    $vals_arr['is_my_account_page']     = isset($page['is_my_account_page']) ? 1 : 0;
                    $vals_arr['account_page_is_parent'] = isset($page['account_page_is_parent']) ? 1 : 0;

                    $page_id = null;

                    if ($page['id'] == '0') {
                        $wpdb->insert($table_prefix . PROPELLER_PAGES_TABLE, $vals_arr);
                        $page_id = $wpdb->insert_id;
                    } else {
                        $wpdb->update(
                            $table_prefix . PROPELLER_PAGES_TABLE,
                            $vals_arr,
                            array(
                                'id' => $page['id']
                            )
                        );

                        $page_id = $page['id'];
                    }


                    // if ($page_id && isset($page['slugs']) && count($page['slugs'])) {
                    //     foreach ($page['slugs']['slug_id'] as $index => $slug_id) {
                    //         if (!empty($page['slugs']['slug'][$index])) {
                    //             $slug_vals = [
                    //                 'page_id' => $page_id,
                    //                 'language' => $page['slugs']['slug_lang'][$index],
                    //                 'slug' => $page['slugs']['slug'][$index]
                    //             ];

                    //             if ($page['slugs']['slug_exists'][$index] == '0')
                    //                 $wpdb->insert($table_prefix . PROPELLER_SLUGS_TABLE, $slug_vals);
                    //             else
                    //                 $wpdb->update($table_prefix . PROPELLER_SLUGS_TABLE, $slug_vals,
                    //                 array(
                    //                     'id' => $slug_id
                    //                 ));
                    //         }
                    //     }
                    // }
                }

                // Propeller::register_pages();
                // Propeller::register_settings();
                // Propeller::register_behavior();

                PageController::create_pages();

                flush_rewrite_rules();

                $message = __('Pages saved', 'propeller-ecommerce-v2');
            } catch (Exception $ex) {
                $success = false;
                $message = $ex->getMessage();
            }
        } else {
            $success = false;
            $message = __('Not enought rights to modify plugin pages', 'propeller-ecommerce-v2');
        }

        die(json_encode(['success' => $success, 'message' => $message]));
    }

    public function save_behavior()
    {
        global $table_prefix, $wpdb;

        $success = true;
        $message = '';

        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'propel-ajax-nonce'))
            die(json_encode(['success' => false, 'message' => __('Security check failed', 'propeller-ecommerce-v2')]));

        if (current_user_can('manage_options')) {
            try {
                $data = $this->sanitize($_POST);

                $use_sso = isset($data['use_sso']) ? 1 : 0;
                $sso_provider = $use_sso == 1 ? 'Firebase' : '';
                $sso_data = null;

                if ($use_sso == 1) {
                    $sso_provider = 'Firebase';
                    $sso_data = isset($data[$sso_provider]) ? json_encode($data[$sso_provider]) : null;
                }

                $vals_arr = array(
                    'wordpress_session' => isset($data['wordpress_session']) ? 1 : 0,
                    'closed_portal' => isset($data['closed_portal']) ? 1 : 0,
                    'semiclosed_portal' => isset($data['semiclosed_portal']) ? 1 : 0,
                    'excluded_pages' => $data['excluded_pages'],
                    'track_user_attr' => $data['track_user_attr'],
                    'track_company_attr' => $data['track_company_attr'],
                    'track_product_attr' => $data['track_product_attr'],
                    'track_category_attr' => $data['track_category_attr'],
                    'reload_filters' => 0,
                    'use_recaptcha' => isset($data['use_recaptcha']) ? 1 : 0,
                    'recaptcha_site_key' => $data['recaptcha_site_key'],
                    'recaptcha_secret_key' => $data['recaptcha_secret_key'],
                    'recaptcha_min_score' => $data['recaptcha_min_score'],
                    'register_auto_login' => isset($data['register_auto_login']) ? 1 : 0,
                    'assets_type' => (int) $data['assets_type'],
                    'stock_check' => isset($data['stock_check']) ? 1 : 0,
                    'load_specifications' => isset($data['load_specifications']) ? 1 : 0,
                    'ids_in_urls' => isset($data['ids_in_urls']) ? 1 : 0,
                    'partial_delivery' => isset($data['partial_delivery']) ? 1 : 0,
                    'selectable_carriers' => isset($data['selectable_carriers']) ? 1 : 0,
                    'use_datepicker' => isset($data['use_datepicker']) ? 1 : 0,
                    'edit_addresses' => isset($data['edit_addresses']) ? 1 : 0,
                    'lang_for_attrs' => isset($data['lang_for_attrs']) ? 1 : 0,
                    'lazyload_images' => isset($data['lazyload_images']) ? 1 : 0,
                    'anonymous_orders' => isset($data['anonymous_orders']) ? 1 : 0,
                    'pdp_new_window' => $data['pdp_new_window'],
                    'icp_country' => $data['icp_country'],
                    'onacc_payments' => $data['onacc_payments'],
                    'default_incl_vat' => isset($data['default_incl_vat']) ? 1 : 0,
                    'show_actioncode' => isset($data['show_actioncode']) ? 1 : 0,
                    'show_order_type' => isset($data['show_order_type']) ? 1 : 0,
                    'default_sort_column' => $data['default_sort_column'],
                    'secondary_sort_column' => $data['secondary_sort_column'],
                    'default_sort_direction' => $data['default_sort_direction'],
                    'default_offset' => $data['default_offset'],
                    'use_sso' => $use_sso,
                    'sso_provider' => $sso_provider,
                    'sso_data' => $sso_data,
                    'pac_add_contacts' => isset($data['pac_add_contacts']) ? 1 : 0,
                    'use_ga4' => isset($data['use_ga4']) ? 1 : 0,
                    'ga4_tracking' => isset($data['ga4_tracking']) ? 1 : 0,
                    'ga4_key' => $data['ga4_key'],
                    'gtm_key' => $data['gtm_key'],
                    'use_cxml' => isset($data['use_cxml']) ? 1 : 0,
                    'cxml_contact_id' => $data['cxml_contact_id'],
                );

                if ($data['setting_id'] == '0')
                    $wpdb->insert($table_prefix . PROPELLER_BEHAVIOR_TABLE, $vals_arr);
                else
                    $wpdb->update(
                        $table_prefix . PROPELLER_BEHAVIOR_TABLE,
                        $vals_arr,
                        array(
                            'id' => $data['setting_id']
                        )
                    );

                $message = __('Behavior saved', 'propeller-ecommerce-v2');
            } catch (Exception $ex) {
                $success = false;
                $message = $ex->getMessage();
            }
        } else {
            $success = false;
            $message = __('Not enought rights to modify plugin behavior', 'propeller-ecommerce-v2');
        }

        die(json_encode(['success' => $success, 'message' => $message]));
    }

    public function ajax_destroy_caches()
    {
        $this->destroy_caches(true);
    }

    public function flush_rw_rules()
    {
        $success = true;
        $message = '';

        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'propel-ajax-nonce'))
            die(json_encode(['success' => false, 'message' => __('Security check failed', 'propeller-ecommerce-v2')]));

        if (current_user_can('manage_options')) {
            try {
                flush_rewrite_rules();

                $message = __('Rewrite rules flushed', 'propeller-ecommerce-v2');
            } catch (Exception $ex) {
                $success = false;
                $message = $ex->getMessage();
            }
        } else {
            $success = false;
            $message = __('Not enought rights to flush rewrite rules', 'propeller-ecommerce-v2');
        }

        die(json_encode(['success' => $success, 'message' => $message]));
    }

    public function destroy_caches($return_json = true)
    {
        global $table_prefix, $wpdb;

        $success = true;
        $message = '';

        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'propel-ajax-nonce'))
            die(json_encode(['success' => false, 'message' => __('Security check failed', 'propeller-ecommerce-v2')]));

        if (current_user_can('manage_options')) {
            try {
                $wpdb->query($wpdb->prepare("DELETE FROM %i  
                                             WHERE option_name LIKE %s  
                                                OR option_name LIKE %s", $table_prefix . "options", '_transient_propeller%', '_transient_timeout_propeller%'));

                do_action('propel_cache_destroyed');

                $message = __('Caches cleared', 'propeller-ecommerce-v2');
            } catch (Exception $ex) {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->clear_generated_files();
        } else {
            $success = false;
            $message = __('Not enought rights to destroy plugin caches', 'propeller-ecommerce-v2');
        }

        if ($return_json)
            die(json_encode(['success' => $success, 'message' => $message]));
    }

    private function clear_generated_files()
    {
        $js_min = PROPELLER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'propel.min.js';
        $css_min = PROPELLER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'propel.min.css';

        if (file_exists($js_min))
            @wp_delete_file($js_min);

        if (file_exists($css_min))
            @wp_delete_file($css_min);
    }

    public function sanitize($data)
    {
        return PropellerUtils::sanitize($data);
    }

    public function register_actions()
    {
        $translator = new PropellerTranslations();
        $sitemap = new PropellerSitemap();
        $valuesets = new PropellerValuesets();

        add_action('wp_ajax_save_translations', array($translator, 'save_translations'));

        add_action('wp_ajax_scan_translations', array($translator, 'scan_translations'));

        add_action('wp_ajax_generate_translations', array($translator, 'generate_translations'));

        add_action('wp_ajax_create_translations_file', array($translator, 'create_translations_file'));

        add_action('wp_ajax_restore_translations', array($translator, 'restore_translations'));

        add_action('wp_ajax_load_translations_backups', array($translator, 'load_translations_backups'));

        add_action('wp_ajax_save_propel_settings', array($this, 'save_settings'));

        add_action('wp_ajax_save_propel_pages', array($this, 'save_pages'));

        add_action('wp_ajax_save_propel_behavior', array($this, 'save_behavior'));

        add_action('wp_ajax_propel_destroy_caches', array($this, 'ajax_destroy_caches'));

        add_action('wp_ajax_propel_flush_rw_rules', array($this, 'flush_rw_rules'));

        add_action('wp_ajax_propel_generate_sitemap', array($sitemap, 'build_sitemap'));

        add_action('wp_ajax_propel_sync_valuesets', array($valuesets, 'sync_valuesets'));
    }
}
