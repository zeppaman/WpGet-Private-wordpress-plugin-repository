import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import 'rxjs/add/operator/toPromise';
import {Message} from 'primeng/components/common/api';
import {MessageService} from 'primeng/components/common/messageservice';
import { environment } from '../../environments/environment';
import { Observable } from 'rxjs/Observable';


@Injectable()
export class PackageService {


  constructor(private http: Http, private messageService: MessageService) { }

  getList(reposlug:string, name:string) {
    return this.http.get(environment.apiHost+ 'api/package/All')
    .toPromise()
    .then(res => {
     
      let items = <any[]> res.json();
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
