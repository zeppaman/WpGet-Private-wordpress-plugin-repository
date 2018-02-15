import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import 'rxjs/add/operator/toPromise';
import {Message} from 'primeng/components/common/api';
import {MessageService} from 'primeng/components/common/messageservice';

import { Observable } from 'rxjs/Observable';
import { ConfigurationService } from './configuration.service';

import { HttpClient } from '@angular/common/http';
@Injectable()
export class RepositoryService {


  constructor(private http: HttpClient, private messageService: MessageService, private config:ConfigurationService) { }
  slugify(text:string)
  {
    if(text==null) return "";
     return text.toLowerCase()
     .replace(/\s+/g, '-')           // Replace spaces with -
     .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
     .replace(/\-\-+/g, '-')         // Replace multiple - with single -
     .replace(/^-+/, '')             // Trim - from start of text
     .replace(/-+$/, '');            // Trim - from end of text
  }
  
  getList() {
    return this.http.get(this.config.apiHost+ 'api/repository/All')
    .map(res => {
      console.log(res);
      return <any[]> res;

    }).toPromise()
    .then(data => data);
  }

  save(item:any)  {
    console.log(item);
    return this.http.post(this.config.apiHost+'api/repository/Item', item)
    .catch((err: Response) => {
        this.messageService.add({severity: 'error', 
        summary: 'Repository Save Erorr', 
        detail: 'Error during save.'  });

        return Observable.throw(new Error("Error saving Repository"));
     }).toPromise(). then(data =>{
      this.messageService.add({severity: 'success', summary: 'Repository Saved', detail: 'Repository' + item.reposlug + ' has been saved'
     + JSON.stringify(item)});
      return data[0];
     })   ; 
      
  }

  delete(item:any)  {
    console.log(item);
    return this.http.delete(this.config.apiHost+'api/repository/Item/'+item.id)
    .toPromise()
   
    .then(data => { 
      this.messageService.add({severity:'success', summary:'User deleted', detail: 'User' + item.username + ' has been deleted'});
      return data[0];
    });
    
  }

}
