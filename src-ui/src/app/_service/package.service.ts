import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import 'rxjs/add/operator/toPromise';
import {Message} from 'primeng/components/common/api';
import {MessageService} from 'primeng/components/common/messageservice';
import { Observable } from 'rxjs/Observable';
import { ConfigurationService } from './configuration.service';
import { HttpClient } from '@angular/common/http';

@Injectable()
export class PackageService {


  constructor(private http: HttpClient, private messageService: MessageService, private config:ConfigurationService) { }

  getList(reposlug:string, name:string) {
    return this.http.get(this.config.apiHost+ 'api/package/All')
    .map(res => {
      
      return <any[]> res;

    })
    .toPromise()
    .then(items => {
     
    
      let results:any[]=[];

      items.forEach(element => {
        if(
          ( reposlug == null || reposlug=="" || element.reposlug === reposlug) &&
          ( name == null || name=="" || element.name.indexOf(name)>= 0 )
        )
          {
            results.push(element);
          }
        
        
      });

      return results;
    })
    .then(data => data);
  }


}
