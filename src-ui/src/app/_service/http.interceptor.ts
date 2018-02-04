
import { Observable } from 'rxjs/Observable';
import { Injectable } from '@angular/core';
import { HttpEvent, HttpInterceptor, HttpHandler, HttpRequest, HttpHeaders } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { APP_BASE_HREF } from '@angular/common';
import { AuthenticationService } from './authentication.services';
import { ConfigurationService } from './configuration.service';
import { HttpResponse } from '@angular/common/http';
import { HttpErrorResponse } from '@angular/common/http';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
    forceLogout(): any {
        localStorage.clear();
        document.location.href= this.config.baseHref;
    }

    constructor(private auth:AuthenticationService,private config:ConfigurationService)
    {
      
    }

    intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
  
        console.log(this.config.baseHref);
       
        if( req.url.indexOf("/api/")>-1 || req.url.indexOf("/catalog/")>-1)
        {
          
            if (this.auth.currentUser()  ) {
             
                let url=req.url;
                return next.handle(req.clone({
                    setHeaders: {
                        'Authorization': 'Bearer '+this.auth.currentUser().token
                    }
                }))
                .catch((err, source) => {
                    if (err.status  == 401 || err.status  == 0) {
                        this.forceLogout();
                           return Observable.empty();
                       } else {
                           return Observable.throw(err);
                   }  
                   });
            }
            else 
            {
                //completely force logout
                this.forceLogout();
                return;
            }
        }
        else
        {
            return next.handle(req);
        }
    }
}