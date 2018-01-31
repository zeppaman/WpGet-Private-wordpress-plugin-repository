import { Injectable } from '@angular/core';
import {Http} from "@angular/http";
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { environment } from '../../environments/environment.prod';
import { HttpClient } from '@angular/common/http';
@Injectable()
export class ConfigurationService {
    apiHost:string;
    baseHref:string;
    installed:boolean;
    constructor( private http: Http) {
    }

    load() {
        return new Promise((resolve) => {
            this.http.get('assets/settings.json').map(res => res.json())
              .subscribe(config => {
                        
                this.apiHost=config.apiHost;
                this.baseHref=config.baseHref;
                this.installed=config.installed;
                if(this.installed==false && environment.production==true)
                {
                    window.location.href= this.apiHost+'api/install';
                }
                resolve();
              });
          });
     
    }

}